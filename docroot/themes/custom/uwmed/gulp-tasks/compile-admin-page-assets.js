/**
 * @file
 * Task: Compile: Sass.
 */

 /* global module */

var opts = {
    admin_page: {
        sass_files: [
            './src/adminimal_theme/styles.scss'
        ],
        sass_dest: './dist/adminimal_theme/assets'
    }
};

module.exports = function (gulp, plugins) {
  'use strict';


    gulp.task('compile:admin-page-assets', function () {


        return gulp.src(
                opts.admin_page.sass_files
            )
                .pipe(plugins.plumber())
                //.pipe(plugins.sourcemaps.init())
                .pipe(plugins.sass({
                    errLogToConsole: true,
                    outputStyle: 'expanded'
                    // includePaths: options.sass.bootstrapFiles
                }))
                //.pipe(plugins.sourcemaps.write({includeContent: false}))
                //.pipe(plugins.sourcemaps.init({loadMaps: true}))
                .pipe(plugins.autoprefixer({
                    browsers: ['last 2 versions'],
                    cascade: false
                }))
                //.pipe(plugins.sourcemaps.write())
                .pipe(gulp.concat('styles.css'))
                .pipe(gulp.dest(opts.admin_page.sass_dest));
        });




};




