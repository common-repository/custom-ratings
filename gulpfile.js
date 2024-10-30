/*
var browserify = require('browserify'),
    watchify = require('watchify'),
    gulp = require('gulp'),
    source = require('vinyl-source-stream'),
    sourceFile = './js/main.js',
    destFolder = './js/',
    destFile = 'findem.js';
*/

var sass = require('gulp-sass');
var browserify = require('gulp-browserify');
var gulp = require('gulp');
var inject = require('gulp-inject');
//var ractiveify = require('ractiveify');
//var gulpif = require('gulpif');
var merge = require('merge-stream');
//var process.env.NODE_ENV || 'development';
var outputDir = 'builds/development';


// Include plugins
var plugins = require("gulp-load-plugins")({
  pattern: ['gulp-*', 'gulp.*', 'main-bower-files'],
  replaceString: /\bgulp[\-.]/
});

// Define default destination folder
var public_dest = 'public/';
var admin_dest = 'admin/';
/*
  // Gulp Dependencies
  var gulp = require('gulp');
  var rename = require('gulp-rename');

  // Build Dependencies
  var browserify = require('gulp-browserify');
  var uglify = require('gulp-uglify');

  // Style Dependencies
  var less = require('gulp-less');
  var prefix = require('gulp-autoprefixer');
  var minifyCSS = require('gulp-minify-css');
 */
/*
gulp.task('browserify', function() {
  return browserify(sourceFile)
  .bundle()
  .pipe(source(destFile))
  .pipe(gulp.dest(destFolder));
});
*/

gulp.task('copypublic', function() {
   gulp.src('./bower_components/ractive/ractive.js')
   .pipe(gulp.dest('./public/js'));
});

gulp.task('copyadmin', function() {
   gulp.src('./bower_components/spectrum/spectrum.js')
   .pipe(gulp.dest('./admin/js'));

   gulp.src('./bower_components/spectrum/spectrum.css')
   .pipe(gulp.dest('./admin/css'));
});


gulp.task('js', function(){
  /*
  gulp.src(plugins.mainBowerFiles())
    .pipe(plugins.filter('*.js'))
    .pipe(gulp.dest(public_dest + 'js'));

  gulp.src(plugins.mainBowerFiles())
    .pipe(plugins.filter('*.css'))
    .pipe(gulp.dest(admin_dest + 'css'));

  
  var public_path = gulp.src('public/js/custom-ratings-public.js')
    .pipe(browserify({debug: true}))
    .pipe(gulp.dest('public/js/builds/custom-ratings-public-build.js'));

  var admin_path = gulp.src('admin/js/custom-ratings-admin.js')
    .pipe(browserify({debug: true}))
    .pipe(gulp.dest('admin/js/builds/custom-ratings-admin-build.js'));

  return merge(public_path, admin_path);
  
  
  return gulp.src('src/js/main.js')
    .pipe(browserify({debug: env === 'development'}))
    .pipe(gulpif(env === 'production',uglify())
    .pipe(gulp.dest('builds/development/js'));
  */
});


gulp.task('sass', function () {
  var config = {};
  /*
  if (env === 'development') {
    config.sourceComments = 'map';
  }

  if (env === 'production') {
    config.outputStyle = 'compressed';
  }
  */

  var public_path = gulp.src('public/scss/custom-ratings-public.scss')
    .pipe(inject(gulp.src(['bower_components/sass-flex/_sass_flexbox.scss','bower_components/ispinner/ispinner.sass'], {read: false}), {
      starttag: '/* inject:imports */',
      endtag: '/* endinject */',
      transform: function (filepath) {
          return '@import ".' + filepath + '";';
      }
    }))
    //.pipe(inject(gulp.src(['bower_components/ispinner/ispinner.css'], {read: false}), {name: 'ispinner'}))
    .pipe(sass(config))
    .pipe(gulp.dest('public/css'));

  var admin_path = gulp.src('admin/scss/custom-ratings-admin.scss')
    .pipe(inject(gulp.src(['bower_components/sass-flex/_sass_flexbox.scss'], {read: false}), {
      starttag: '/* inject:imports */',
      endtag: '/* endinject */',
      transform: function (filepath) {
          return '@import ".' + filepath + '";';
      }
    }))    
    .pipe(sass(config))
    .pipe(gulp.dest('admin/css'));
 
  return merge(public_path, admin_path);

});
 
gulp.task('watch', function() {
  gulp.watch('**/js/*.js', ['js']);
  gulp.watch('**/scss/*.scss', ['sass']);
});


gulp.task('default', ['js', 'sass', 'copypublic', 'copyadmin', 'watch']);