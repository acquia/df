var gulp     = require('gulp');
var $        = require('gulp-load-plugins')();
var gulpCopy = require('gulp-copy');
var concat   = require('gulp-concat');

var sassPaths = [
  'bower_components/foundation-sites/scss',
  'bower_components/motion-ui/src',
];

gulp.task('sass', function() {
  return gulp.src('scss/dfs_base.scss')
    .pipe($.sass({
      includePaths: sassPaths
    })
      .on('error', $.sass.logError))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9']
    }))
    .pipe(gulp.dest('css'));
});

// move vendor js files from bower_components into /js/ folder for deployment purposes
gulp.task('copy', function() {
return gulp.src(['bower_components/foundation-sites/dist/*min.js', 'bower_components/motion-ui/dist/*min.js','bower_components/what-input/*min.js'])
  .pipe(gulpCopy('js/vendor',{prefix: 3}));
});

gulp.task('javascript', function() {
  return gulp.src(PATHS.javascript)
    .pipe($.sourcemaps.init())
    .pipe($.babel()) // <-- There it is!
    .pipe($.concat('app.js'))
    .pipe(uglify)
    .pipe($.if(!isProduction, $.sourcemaps.write()))
    .pipe(gulp.dest('dist/assets/js'))
    .on('finish', browser.reload);
});

// concatanate all vendor scripts into a single js file. specific order is defined
gulp.task('concat', function() {
  return gulp.src(['./js/vendor/what-input.min.js','./js/vendor/motion-ui.min.js','./js/vendor/foundation.min.js'])
    .pipe(concat('vendor.all.js'))
    .pipe(gulp.dest('./js/'));
});

gulp.task('default', ['sass'], function() {
  gulp.watch(['scss/**/*.scss'], ['sass']);
});
