var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var concat = require('gulp-concat');
var less = require('gulp-less');
var sourcemaps = require('gulp-sourcemaps');
var debug = require('gulp-debug');

var env = process.env.GULP_ENV;

var paths = {
    js: [
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/admin-lte/dist/js/app.min.js'
    ],
    'less': [
        'src/Sylius/Bundle/UiBundle/Resources/assets/less/**'
    ],
    css: [
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'node_modules/admin-lte/dist/css/AdminLTE.min.css',
        'web/test/*'
    ]
};

gulp.task('js', function () {
    return gulp.src(paths.js)
        .pipe(concat('javascript.js'))
        .pipe(gulpif('prod' === env, uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/js'))
    ;
});

gulp.task('less', function () {
    return gulp.src(paths.less)
        .pipe(less())
        .pipe(concat('less-compiled.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/test'))
    ;
});

gulp.task('css', function () {
    return gulp.src(paths.css)
        .pipe(concat('style.css'))
        .pipe(gulpif('prod' === env, uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'))
    ;
});

gulp.task('watch', function() {
    gulp.watch(paths.js, ['js']);
    gulp.watch(paths.less, ['less', 'css']);
    gulp.watch(paths.css, ['css']);
});

gulp.task('default', ['watch', 'js', 'less', 'css']);
