const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

mix.setPublicPath('dist')
mix.setResourceRoot('resources')
mix.sourceMaps()

mix.disableSuccessNotifications()
