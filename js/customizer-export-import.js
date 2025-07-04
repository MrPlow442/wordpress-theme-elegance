(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Export functionality
        $('#select-all-export').on('change', function() {
            $('.export-section-checkbox').prop('checked', this.checked);
            updateExportButton();
        });
        
        $('.export-section-checkbox').on('change', function() {
            updateExportButton();
            updateSelectAllExport();
        });
        
        function updateExportButton() {
            const checkedBoxes = $('.export-section-checkbox:checked').length;
            $('#export-selected-sections').prop('disabled', checkedBoxes === 0);
        }
        
        function updateSelectAllExport() {
            const totalBoxes = $('.export-section-checkbox').length;
            const checkedBoxes = $('.export-section-checkbox:checked').length;
            $('#select-all-export').prop('checked', totalBoxes === checkedBoxes);
        }
        
       $('#export-selected-sections').on('click', function(e) {
            e.preventDefault();
            
            const selectedSections = [];
            $('.export-section-checkbox:checked').each(function() {
                selectedSections.push($(this).val());
            });
            
            if (selectedSections.length === 0) {
                alert('Please select at least one section to export.');
                return;
            }
            
            // Build URL with selected sections as parameters
            let exportUrl = customizerExportImport.customizerURL + '?elegance-export=' + customizerExportImport.exportNonce;
            selectedSections.forEach(function(section) {
                exportUrl += '&export_sections[]=' + encodeURIComponent(section);
            });
            
            // Redirect to customizer with export parameters
            window.location.href = exportUrl;
        });

        
        // Import functionality
        let importData = null;

        $('#import-file').on('change', function() {
            const file = this.files[0];
            if (!file) return alert('Please select a file first.');
            const reader = new FileReader();
            reader.onload = function(evt) {
                try {
                    importData = JSON.parse(evt.target.result);
                } catch (err) {
                    importData = null;
                    return alert('Invalid JSON file format.');
                }
                showImportPreview(importData);
            };
            reader.readAsText(file);
        });

        function showImportPreview(data) {
            if (!data.theme || !data.sections_data) {
                return alert('Invalid import file format.');
            }
            // Theme mismatch warning
            if (data.theme !== customizerExportImport.currentTheme) {
                if (!confirm('This file is for theme "' + data.theme + '". Continue?')) {
                    return;
                }
            }
            // Build preview HTML
            let html = '<strong>Exported:</strong> ' + data.exported + '<br>';
            html += '<strong>Sections:</strong> ' 
                + Object.keys(data.sections_data).length + '<br><ul>';
            $.each(data.sections_data, function(sec, settings) {
                html += '<li><label><input type="checkbox" class="import-section-checkbox" value="'
                    + sec + '"> ' + sec + ' (' + Object.keys(settings).length + ')</label></li>';
            });
            html += '</ul>';
            $('#import-sections-list').html(html);
            $('#import-sections-preview').show();
            updateImportButton();
        }

        // Enable/disable Import button
        function updateImportButton() {
            const any = $('.import-section-checkbox:checked').length > 0;
            $('#import-selected-sections').prop('disabled', !any);
        }

        // “Select All” for import
        $('#select-all-import').on('change', function() {
            $('.import-section-checkbox').prop('checked', this.checked);
            updateImportButton();
        });

        // Individual import checkbox change
        $(document).on('change', '.import-section-checkbox', updateImportButton);

        // Execute import
        $('#import-selected-sections').on('click', function(e) {
            e.preventDefault();
            const sections = [];
            $('.import-section-checkbox:checked').each(function() {
                sections.push($(this).val());
            });
            if (!sections.length) {
                return alert('Please select at least one section to import.');
            }
            if (!importData) {
                return alert('No import data loaded.');
            }
            // Apply via Customizer API
            let imported = 0, skipped = 0;
            $.each(importData.sections_data, function(secId, settings) {
                if (sections.indexOf(secId) === -1) {
                    return skipped++;
                }
                $.each(settings, function(settingId, value) {
                    try {
                        const setting = wp.customize(settingId);
                        if (setting) {
                            setting.set(value);
                            imported++;
                        } else {
                            skipped++;
                        }
                    } catch (err) {
                        skipped++;
                    }
                });
            });
            alert('Imported ' + imported + ' settings, skipped ' + skipped + '.');
            // Refresh preview
            if (wp.customize && wp.customize.previewer) {
                wp.customize.previewer.refresh();
            }
        });
    });
    
})(jQuery);
