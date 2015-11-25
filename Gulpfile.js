var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');

var debug = require('gulp-debug');

var env = process.env.GULP_ENV;

gulp.task('js', function () {
    return gulp.src([
        'vendor/bower_components/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js',
        'vendor/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js',
        'vendor/bower_components/AdminLTE/dist/js/app.min.js'
    ])
        .pipe(concat('javascript.js'))
        .pipe(gulpif('prod' === env, uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/js'))
    ;
});

gulp.task('css', function () {
    return gulp.src([
        'vendor/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css',
        'vendor/bower_components/AdminLTE/dist/css/AdminLTE.min.css',
        'src/Sylius/Bundle/UiBundle/Resources/assets/css/**'
    ])
        .pipe(concat('style.css'))
        .pipe(gulpif('prod' === env, uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'))
    ;
});

gulp.task('default', ['js', 'css']);
