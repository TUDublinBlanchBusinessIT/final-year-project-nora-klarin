const defaultTheme = require('tailwindcss/defaultTheme')

const forms = require('@tailwindcss/forms')



/** @type {import('tailwindcss').Config} */

module.exports = {

    content: [

        './resources/views/**/*.blade.php',

        './resources/js/**/*.js',

        './resources/js/**/*.vue',

        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',

        './storage/framework/views/*.php',

    ],



    theme: {

        extend: {

            fontFamily: {

                sans: ['Figtree', ...defaultTheme.fontFamily.sans],

            },

        },

    },



    plugins: [forms],

}