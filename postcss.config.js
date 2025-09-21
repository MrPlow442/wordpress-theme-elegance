const path = require('path');

module.exports = ({ env }) => {
  const isProduction = env === 'production';  
  return {
    plugins: [
      require('autoprefixer')({
        overrideBrowserslist: [
          '> 1%',
          'last 2 versions',
          'not dead',
          'not ie <= 11'
        ]
      }),
      require('cssnano')({
        preset: ['default', {
          discardComments: {
            removeAll: true
          },
          normalizeWhitespace: true
        }]
      }),
      ...(isProduction ? [
        require('@fullhuman/postcss-purgecss')({
          content: [
            path.join(__dirname, './**/*.php'),
            path.join(__dirname, './src/js/**/*.js'),
            path.join(__dirname, './template-parts/**/*.php'),
            path.join(__dirname, './inc/**/*.php')
          ],
          defaultExtractor: content => {
            const broadMatches = content.match(/[^<>"'`\s]*[^<>"'`\s:]/g) || [];
            const innerMatches = content.match(/[^<>"'`\s.()]*[^<>"'`\s.():]/g) || [];
            return broadMatches.concat(innerMatches);
          },
          safelist: {
            standard: [
              /^wp-/,
              /^admin-/,
              /^logged-in/,
              /^home/,
              /^page-/,
              /^post-/,
              /^category-/,
              /^tag-/,
              /^single/,
              /^archive/,
              /^search/,
              /^error404/,
              /^customize-/,
              /^js/,
              /^no-js/,
              /^rtl/,
              /^screen-reader-text/,
              /^animate/,
              /^animated/,
              /^bounce/,
              /^fade/,
              /^slide/,
              /^hidden/,
              /^visible/,
              /^active/,
              /^current/,
              /^open/,
              /^closed/,
              /^swiper/,
              /^font-/,
              /^raleway-/
            ],
            deep: [
              /owl-/,
              /fa-/,
              /fas-/,
              /raleway/,
              /fontsource/,
              /btn-/,
              /scroll-/,
              /mobile-menu/,
              /sidemenu/,
              /^swiper/              
            ]
          }
        })
      ] : [])
    ]
  };
};