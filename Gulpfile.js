var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var merge = require('merge-stream');
var debug = require('gulp-debug');
var livereload = require('gulp-livereload');

var env = process.env.GULP_ENV;
var rootPath = 'web/assets/';

var paths = {
    js: [
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/admin-lte/dist/js/app.min.js',
    ],
    sass: [
        'src/Sylius/Bundle/UiBundle/Resources/private/sass/**',
    ],
    css: [
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'node_modules/admin-lte/dist/css/AdminLTE.min.css',
        'node_modules/font-awesome/css/font-awesome.min.css',
    ],
    fonts: [
        'node_modules/font-awesome/fonts/**',
    ],
    img: [
        'src/Sylius/Bundle/UiBundle/Resources/private/img/**',
    ]
};

gulp.task('js', function () {
    return gulp.src(paths.js)
        .pipe(concat('javascript.js'))
        .pipe(gulpif('prod' === env, uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(rootPath + 'js'))
    ;
});

gulp.task('css', function() {
    var sassStream = gulp.src(paths.sass)
        .pipe(sass())
        .pipe(concat('sass-files.scss'))
    ;

    var cssStream = gulp.src(paths.css)
        .pipe(concat('css-files.css'))
    ;

    return merge(sassStream, cssStream)
        .pipe(concat('style.css'))
        .pipe(gulpif('prod' === env, uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(rootPath + 'css'))
        .pipe(livereload())
    ;
});

gulp.task('fonts', function () {
    return gulp.src(paths.fonts)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(rootPath + 'fonts'))
    ;
});

gulp.task('img', function () {
    return gulp.src(paths.img)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(rootPath + 'img'))
    ;
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch(paths.js, ['js']);
    gulp.watch(paths.sass, ['css']);
    gulp.watch(paths.css, ['css']);
    gulp.watch(paths.img, ['img']);
});

gulp.task('default', ['watch', 'js', 'css', 'fonts', 'img']);
