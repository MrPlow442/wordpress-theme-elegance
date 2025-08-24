import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig(({ command, mode }) => {
  const isProduction = mode === 'production';
  const isDevelopment = mode === 'development';

  const getAssetSuffix = () => {
    return isProduction ? '.min' : '';
  }

  return {  
    root: 'src',
    base: './',
    build: {
      outDir: '../assets',
      emptyOutDir: true,
      rollupOptions: {
        input: {
          main: resolve(__dirname, 'src/js/main.js'),
          // admin: resolve(__dirname, 'src/js/admin.js'),        
          'customizer-export-import': resolve(__dirname, 'src/js/customizer-export-import.js'),
          'customizer-preview': resolve(__dirname, 'src/js/customizer-preview.js'),
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
      minify: isProduction ? 'esbuild' : false,
      target: 'es2020',
      cssMinify: isProduction ? 'esbuild' : false,
      sourcemap: isDevelopment
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
