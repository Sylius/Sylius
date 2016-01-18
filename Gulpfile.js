var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var concat = require('gulp-concat');
var less = require('gulp-less');
var sourcemaps = require('gulp-sourcemaps');
var merge = require('merge-stream');
var debug = require('gulp-debug');
var livereload = require('gulp-livereload');

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
        'node_modules/admin-lte/dist/css/AdminLTE.min.css'
    ],
    'img': [
        'src/Sylius/Bundle/UiBundle/Resources/assets/img/**'
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

gulp.task('css', function() {
    var lessStream = gulp.src(paths.less)
        .pipe(less())
        .pipe(concat('less-files.less'))
    ;

    var cssStream = gulp.src(paths.css)
        .pipe(concat('css-files.css'))
    ;

    return merge(lessStream, cssStream)
        .pipe(concat('style.css'))
        .pipe(gulpif('prod' === env, uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'))
        .pipe(livereload())
    ;
});

gulp.task('img', function () {
    return gulp.src(paths.img)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/img'))
    ;
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch(paths.js, ['js']);
    gulp.watch(paths.less, ['css']);
    gulp.watch(paths.css, ['css']);
    gulp.watch(paths.img, ['img']);
});

gulp.task('default', ['watch', 'js', 'css', 'img']);
