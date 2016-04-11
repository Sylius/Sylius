var gulp = require('gulp');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var debug = require('gulp-debug');
var livereload = require('gulp-livereload');
var order = require('gulp-order');
var merge = require('merge-stream');
var env = process.env.GULP_ENV;

var rootPath = 'web/assets/';

var adminRootPath = rootPath + 'admin/';

var paths = {
    admin: {
        js: [
            'node_modules/jquery/dist/jquery.min.js',
            'node_modules/semantic-ui-css/semantic.min.js',
            'src/Sylius/Bundle/UiBundle/Resources/private/js/**',
            'src/Sylius/Bundle/ShippingBundle/Resources/public/js/**',
            'src/Sylius/Bundle/PromotionBundle/Resources/public/js/sylius-promotion.js',
            'src/Sylius/Bundle/UserBundle/Resources/public/js/sylius-user.js'
        ],
        sass: [
            'src/Sylius/Bundle/UiBundle/Resources/private/sass/**',
        ],
        css: [
            'node_modules/semantic-ui-css/semantic.min.css',
        ],
        img: [
            'src/Sylius/Bundle/UiBundle/Resources/private/img/**',
        ]
    }
};

gulp.task('admin-js', function () {
    return gulp.src(paths.admin.js)
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'js/'))
    ;
});

gulp.task('admin-css', function() {
    gulp.src(['node_modules/semantic-ui-css/themes/**/*']).pipe(gulp.dest(adminRootPath + 'css/themes/'));

    var cssStream = gulp.src(paths.admin.css)
        .pipe(concat('css-files.css'))
    ;

    var sassStream = gulp.src(paths.admin.sass)
        .pipe(sass())
        .pipe(concat('sass-files.scss'))
    ;

    return merge(cssStream, sassStream)
        .pipe(order(['css-files.css', 'sass-files.scss']))
        .pipe(concat('style.css'))
        .pipe(gulpif(env === 'prod', uglifycss))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'css/'))
        .pipe(livereload())
    ;
});

gulp.task('admin-img', function() {
    return gulp.src(paths.admin.img)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'img/'))
    ;
});

gulp.task('admin-watch', function() {
    livereload.listen();

    gulp.watch(paths.admin.js, ['admin-js']);
    gulp.watch(paths.admin.sass, ['admin-css']);
    gulp.watch(paths.admin.css, ['admin-css']);
    gulp.watch(paths.admin.img, ['admin-img']);
});

gulp.task('default', ['admin-js', 'admin-css', 'admin-img']);
gulp.task('watch', ['admin-watch', 'admin-js', 'admin-css', 'admin-img']);
