( function ( api ) {
	const linkSettingToVar = ( settingId, cssVarName ) => {
		api( settingId, ( value ) => {
			value.bind( ( newVal ) => {
				document.documentElement.style.setProperty( cssVarName, newVal );
			} );
		} );
	};
	
	linkSettingToVar( 'gradient_color_1', '--gradient-color-1' );
	linkSettingToVar( 'gradient_color_2', '--gradient-color-2' );	
	linkSettingToVar( 'global_text_color', '--text-color' );
} )( wp.customize );
