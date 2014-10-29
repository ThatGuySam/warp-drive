# [Dominate Theme](http://guts.church/)

Dominate is the Guts Church WordPress theme based on [Roots](http://roots.io/)

* Source: [https://github.com/GutsChurch/dominate](https://github.com/GutsChurch/dominate)
* Roots Documentation: [http://roots.io/docs/](http://roots.io/docs/)

## Features

* [Grunt](http://roots.io/using-grunt-for-wordpress-theme-development/) for compiling LESS to CSS, checking for JS errors, live reloading, concatenating and minifying files, versioning assets, and generating lean Modernizr builds
* [Bower](http://bower.io/) for front-end package management
* [HTML5 Boilerplate](http://html5boilerplate.com/)
  * The latest [jQuery](http://jquery.com/) via Google CDN, with a local fallback
  * The latest [Modernizr](http://modernizr.com/) build for feature detection, with lean builds with Grunt
  * An optimized Google Analytics snippet
* [Bootstrap](http://getbootstrap.com/)
* Organized file and template structure
* ARIA roles and microformats
* [Theme wrapper](http://roots.io/an-introduction-to-the-roots-theme-wrapper/)
* Cleaner HTML output of navigation menus
* Posts use the [hNews](http://microformats.org/wiki/hnews) microformat
* [Multilingual ready](http://roots.io/wpml/) and over 30 available [community translations](https://github.com/roots/roots-translations)


### Available Grunt commands

* `grunt dev` — Compile LESS to CSS, concatenate and validate JS
* `grunt watch` — Compile assets when file changes are made
* `grunt build` — Create minified assets that are used on non-development environments
* `grunt build_uncss` — Clean out unused css and create minified assets that are used on non-development environments
