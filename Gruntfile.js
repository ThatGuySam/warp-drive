"use strict";
module.exports = function(grunt) {
  // Load all tasks
  require('load-grunt-tasks')(grunt);
  // Show elapsed time
  require('time-grunt')(grunt);

  var jsFileList = [
//    'assets/vendor/bootstrap/js/transition.js',
//    'assets/vendor/bootstrap/js/alert.js',
//    'assets/vendor/bootstrap/js/button.js',
//    'assets/vendor/bootstrap/js/carousel.js',
//    'assets/vendor/bootstrap/js/collapse.js',
//    'assets/vendor/bootstrap/js/dropdown.js',
//    'assets/vendor/bootstrap/js/modal.js',
//    'assets/vendor/bootstrap/js/tooltip.js',
//    'assets/vendor/bootstrap/js/popover.js',
//    'assets/vendor/bootstrap/js/scrollspy.js',
//    'assets/vendor/bootstrap/js/tab.js',
//    'assets/vendor/bootstrap/js/affix.js',
    'assets/vendor/jquery/dist/jquery.js',
    'assets/vendor/slick.js/slick/slick.js',
    'assets/vendor/selection-sharer/dist/selection-sharer.js',
    'assets/vendor/jquery.countdown/dist/jquery.countdown.js',
    'assets/vendor/masonry/dist/masonry.pkgd.js',
    'assets/vendor/HideSeek/jquery.hideseek.js',
    'assets/vendor/jquery-unveil/jquery.unveil.js',
    'assets/vendor/bootstrap-material-design/dist/js/material.js',
    'assets/vendor/bootstrap-material-design/dist/js/ripples.js',
    'assets/js/plugins/*.js',
    'assets/js/_*.js',
    '../../plugins/js_composer/assets/js/js_composer_front.js',//Visual Composer
  ];
  
  var domain = 'http://localhost:8888/gc-dev/';

  grunt.initConfig({
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: [
        'Gruntfile.js',
        'assets/js/*.js',
        '!assets/js/scripts.js',
        '!assets/**/*.min.*'
      ]
    },
    less: {
      dev: {
        files: {
          'assets/css/main.css': [
            'assets/less/main.less'
          ]
        },
        options: {
          compress: true,
          // LESS source map
          // To enable, set sourceMap to true and update sourceMapRootpath based on your install
          sourceMap: true,
          sourceMapFilename: 'assets/css/main.css.map',
          sourceMapRootpath: '/app/themes/roots/'
        }
      },
      build: {
        files: {
          'assets/css/main.min.css': [
            'assets/less/main.less'
          ]
        },
        options: {
          compress: true
        }
      },
      build_uncss: {
        files: {
          'assets/css/main.min.css': [
            'assets/less/main.less'
          ]
        },
        options: {
          compress: true
        }
      },
      compress: {
        files: {
          'assets/css/main.min.css': [
            'assets/css/main.min.css'
          ]
        },
        options: {
          compress: true
        }
      }
    },
    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: [jsFileList],
        dest: 'assets/js/scripts.js'
      }
    },
    uglify: {
      dist: {
        files: {
          'assets/js/scripts.min.js': [jsFileList]
        }
      }
    },
    autoprefixer: {
      options: {
        browsers: ['last 2 versions', 'ie 8', 'ie 9', 'android 2.3', 'android 4', 'opera 12']
      },
      dev: {
        options: {
          map: {
            prev: 'assets/css/'
          }
        },
        src: 'assets/css/main.css'
      },
      build: {
        src: 'assets/css/main.min.css'
      }
    },
    modernizr: {
      build: {
        devFile: 'assets/vendor/modernizr/modernizr.js',
        outputFile: 'assets/js/vendor/modernizr.min.js',
        files: {
          'src': [
            ['assets/js/scripts.min.js'],
            ['assets/css/main.min.css']
          ]
        },
        uglify: true,
        parseFiles: true
      }
    },
    exec: {
      get_grunt_sitemap: {
        command: 'curl --silent --output sitemap.json '+domain+'?show_sitemap'
      }
    },
    uncss: {
      dist: {
        options: {
          ignore       : [/expanded/,/js/,/wp-/,/align/,/admin-bar/],
          stylesheets  : ['assets/css/main.min.css'],
          ignoreSheets : [/fonts.googleapis/],
          urls         : [] //Overwritten in load_sitemap_and_uncss task
        },
        files: {
          'assets/css/main.min.css': ['**/*.php']
        }
      }
    },
    
    version: {
      default: {
        options: {
          format: true,
          length: 32,
          manifest: 'assets/manifest.json',
          querystring: {
            style: 'roots_css',
            script: 'roots_js'
          }
        },
        files: {
          'lib/scripts.php': 'assets/{css,js}/{main,scripts}.min.{css,js}'
        }
      }
    },
    watch: {
      less: {
        files: [
          'assets/less/*.less',
          'assets/less/**/*.less'
        ],
        tasks: ['less:dev', 'autoprefixer:dev']
      },
      js: {
        files: [
          jsFileList,
          '<%= jshint.all %>'
        ],
        tasks: ['jshint', 'concat']
      },
      livereload: {
        // Browser live reloading
        // https://github.com/gruntjs/grunt-contrib-watch#live-reloading
        options: {
          livereload: true
        },
        files: [
          'assets/css/main.css',
          'assets/js/scripts.js',
          'templates/*.php',
          'lib/*.php',
          'inc/*.php',
          '*.php'
          
        ]
      }
    }
  });
  
  grunt.registerTask('load_sitemap_json', function() {
     var sitemap_urls = grunt.file.readJSON('./sitemap.json');
     grunt.config.set('uncss.dist.options.urls', sitemap_urls);
  });

  // Register tasks
  grunt.registerTask('default', [
    'dev'
  ]);
  grunt.registerTask('dev', [
    'jshint',
    'less:dev',
    'autoprefixer:dev',
    'concat'
  ]);
  grunt.registerTask('build', [
    'jshint',
	'less:build',
    'autoprefixer:build',
    'uglify',
    'modernizr',
    'version'
  ]);
  grunt.registerTask('build_uncss', [
    'jshint',
//	'less:build_uncss',
    'exec:get_grunt_sitemap',
    'load_sitemap_json',
    'uncss:dist',
    'less:compress',
    'autoprefixer:build',
    'uglify',
    'modernizr',
    'version'
  ]);
};
