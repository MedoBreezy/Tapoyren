const mix = require('laravel-mix');

mix.react('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css').sass('resources/sass/admin.scss', 'public/css').options({
        processCssUrls: false
    }).browserSync({
        open: false,
        proxy: process.env.APP_URL,

        files: [
            'app/**/*.php',
            'public/**/*',
            'resources/**/*',
        ]
    });
