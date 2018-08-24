let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');


// sign in style page
mix.sass('resources/assets/sass/sigin.scss', 'public/css');

mix.sass('resources/assets/sass/frontend.scss', 'public/css');


// sass complie ADMIN layout master
mix.js('resources/assets/backend/js/master.js', 'public/backend/js');
mix.sass('resources/assets/backend/sass/backend_build.scss', 'public/backend/css/backend.css');

// admin
mix.copy('resources/assets/backend/img', 'public/backend/img');
mix.copy('resources/assets/plugin', 'public/plugin');
mix.copy('resources/assets/backend/js/asset', 'public/backend/js/asset');


// mix.js([
//     'resources/assets/backend/js/main.js',
// ], 'public/backend/js/backend.js');



