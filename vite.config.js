import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig(({ command, mode }) => {
  const isProduction = mode === 'production';
  const isDevelopment = mode === 'development';

  process.env.NODE_ENV = mode;

  const getAssetSuffix = () => {
    return isProduction ? '.min' : '';
  }

  const RESERVED_PLUGIN_VARIABLES = [
    // MailerLite
    'ml',
    
    // WPForms  
    'wpforms',
    'WPFormsBuilder',
    'wpf',
    
    // WooCommerce
    'wc',
    'woocommerce', 
    'WC',
    'wc_single_product_params',
    'wc_add_to_cart_params',
    'wc_cart_fragments_params',
    'wc_checkout_params',
    'woocommerce_params',
    
    // Jetpack
    'jetpackL10n',
    'JetpackInstantSearch',
    'jetpack',
    'jp',
    
    // Common WordPress globals
    'wp',
    'wpApiSettings',
    'ajaxurl',
    'adminpageL10n',
    
    // jQuery and related
    '$',
    'jQuery',
    'jq',
    
    // Other popular plugins
    'yoast',
    'rankMath',
    'elementor',
    'cf7',
    'acf', 
    'gt',  
    'ga',  
    'gtag',
  ];

  return {  
    root: 'src',
    base: './',
    assetsInclude: [
      '**/*.woff',
      '**/*.woff2',
      '**/*.eot',
      '**/*.ttf',
      '**/*.otf',
      '**/*.svg'
    ],
    esbuild: {
      jsx: 'transform',
      jsxFactory: 'wp.element.createElement',
      jsxFragment: 'wp.element.Fragment'
    },
    build: {
      outDir: '../assets',
      emptyOutDir: true,
      rollupOptions: {
        input: {
          main: resolve(__dirname, 'src/js/main.js'),           
          'customizer-export-import': resolve(__dirname, 'src/js/customizer/customizer-export-import.js'),
          'customizer-preview': resolve(__dirname, 'src/js/customizer/customizer-preview.js'),
          'testimonial-meta': resolve(__dirname, 'src/jsx/testimonial-meta.jsx'),
          style: resolve(__dirname, 'src/scss/main.scss'),
          'admin-style': resolve(__dirname, 'src/scss/admin.scss')          
        },
        output: {
          entryFileNames: (chunkInfo) => {          
            const suffix = getAssetSuffix();
            switch(chunkInfo.name) {
              case 'style':
              case 'admin-style':
                  return `css/[name]${suffix}.min.css`;
            };
            return `js/[name]${suffix}.js`;
          },
          chunkFileNames: 'js/chunks/[name].js',
          assetFileNames: (assetInfo) => {            
            if (assetInfo.name && assetInfo.name.endsWith('.css')) {            
              const suffix = getAssetSuffix();
              return `css/[name]${suffix}[extname]`;
            }
            if (assetInfo.name && /\.(woff|woff2|eot|ttf|otf|svg)$/.test(assetInfo.name)) {
              return 'fonts/[name][extname]';
            }
            
            return 'assets/[name].[extname]';
          }
        }
      },    
      minify: isProduction ? 'terser' : false,
      target: 'es2020',
      cssMinify: isProduction ? 'esbuild' : false,
      sourcemap: isDevelopment,
      terserOptions: {
        mangle: {
          reserved: RESERVED_PLUGIN_VARIABLES,
          properties: false
        },
        compress: {          
          unused: false,          
          keep_fnames: /^(ml|wc|wp|wpforms|jetpack)/i
        }
      }
    },
    css: {
      preprocessorOptions: {
        scss: {
          api: 'modern-compiler'        
        }
      },
      postcss: resolve(__dirname, 'postcss.config.js')
    }
}});
