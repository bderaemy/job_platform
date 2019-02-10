
# Xeer theme

## this a simple sub-theme of bootstrap

It is sass compiled css with laravel-mix.

### Main commands :
Enable automatic compilation when developing :

`$ npm run watch`

Uglify css and js before deploying to prod :

`$ npm run production`



**WARNING:** Do not modify the files inside of
`./xeer/bootstrap` directly. Doing so may cause issues when upgrading the
[Bootstrap Framework] in the future.

## Overrides {#overrides}
The `./xeer/scss/_default-variables.scss` file is generally where you will
spend the majority of your time providing any default variables that should be
used by the [Bootstrap Framework] instead of its own.

The `./xeer/scss/overrides.scss` file contains various Drupal overrides to
properly integrate with the [Bootstrap Framework]. It may contain a few
enhancements, feel free to edit this file as you see fit.

The `./xeer/scss/style.scss` file is the glue that combines:
`_default-variables.scss`, [Bootstrap Framework Source Files] and the 
`overrides.scss` file together. Generally, you will not need to modify this
file unless you need to add or remove files to be imported. This is the file
that you should compile to `./xeer/css/style.css` (note the same file
name, using a different extension of course).

#### See also:
- @link theme_settings Theme Settings @endlink
- @link templates Templates @endlink
- @link plugins Plugin System @endlink

[Bootstrap Framework]: https://getbootstrap.com/docs/3.4/
[Bootstrap Framework Source Files]: https://github.com/twbs/bootstrap-sass
[Sass]: http://sass-lang.com
