var concat = require('gulp-concat');
var env = process.env.GULP_ENV;
var gulp = require('gulp');
var gulpif = require('gulp-if');
var livereload = require('gulp-livereload');
var merge = require('merge-stream');
var order = require('gulp-order');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var argv = require('yargs').argv;
var path = require('path');

var rootPath = argv.rootPath;
var shopRootPath = rootPath + 'shop/';
var vendorPath = argv.vendorPath || '';
var vendorShopPath = '' === vendorPath ? '' : vendorPath + 'ShopBundle/';
var vendorUiPath = '' === vendorPath ? '../UiBundle/' : vendorPath + 'UiBundle/';
var nodeModulesPath = argv.nodeModulesPath;

var paths = {
    shop: {
        js: [
            nodeModulesPath + 'jquery/dist/jquery.min.js',
            nodeModulesPath + 'semantic-ui-css/semantic.min.js',
            nodeModulesPath + 'lightbox2/dist/js/lightbox.js',
            vendorUiPath + 'Resources/private/js/**',
            vendorShopPath + 'Resources/private/js/**'
        ],
        sass: [
            vendorUiPath + 'Resources/private/sass/**',
            vendorShopPath + 'Resources/private/sass/**'
        ],
        css: [
            nodeModulesPath + 'semantic-ui-css/semantic.min.css',
            nodeModulesPath + 'lightbox2/dist/css/lightbox.css',
            vendorUiPath + 'Resources/private/css/**',
            vendorShopPath + 'Resources/private/css/**',
            vendorShopPath + 'Resources/private/scss/**'
        ],
        img: [
            vendorUiPath + 'Resources/private/img/**',
            vendorShopPath + 'Resources/private/img/**'
        ]
    }
};

var sourcePathMap = [
    {
        sourceDir: path.relative('', vendorShopPath + 'Resources/private/'),
        destPath: '/SyliusShopBundle/'
    },
    {
        sourceDir: path.relative('', vendorUiPath + 'Resources/private/'),
        destPath: '/SyliusUiBundle/'
    },
    {
        sourceDir: path.relative('', nodeModulesPath),
        destPath: '/node_modules/'
    }
];

var mapSourcePath = function mapSourcePath(sourcePath, file) {
    for (var spec of sourcePathMap) {
        if (sourcePath.substring(0, spec.sourceDir.length) === spec.sourceDir) {
            return spec.destPath + sourcePath.substring(spec.sourceDir.length);
        }
    }

    return sourcePath;
};

gulp.task('shop-js', function () {
    return gulp.src(paths.shop.js, { base: './' })
        .pipe(gulpif(env !== 'prod', sourcemaps.init()))
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(gulpif(env !== 'prod', sourcemaps.mapSources(mapSourcePath)))
        .pipe(gulpif(env !== 'prod', sourcemaps.write('./')))
        .pipe(gulp.dest(shopRootPath + 'js/'))
    ;
});

gulp.task('shop-css', function() {
    gulp.src([nodeModulesPath + 'semantic-ui-css/themes/**/*']).pipe(gulp.dest(shopRootPath + 'css/themes/'));

    var cssStream = gulp.src(paths.shop.css, { base: './' })
            .pipe(gulpif(env !== 'prod', sourcemaps.init()))
            .pipe(concat('css-files.css'))
        ;

    var sassStream = gulp.src(paths.shop.sass, { base: './' })
            .pipe(gulpif(env !== 'prod', sourcemaps.init()))
            .pipe(sass())
            .pipe(concat('sass-files.scss'))
        ;

    return merge(cssStream, sassStream)
        .pipe(order(['css-files.css', 'sass-files.scss']))
        .pipe(concat('style.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(gulpif(env !== 'prod', sourcemaps.mapSources(mapSourcePath)))
        .pipe(gulpif(env !== 'prod', sourcemaps.write('./')))
        .pipe(gulp.dest(shopRootPath + 'css/'))
        .pipe(livereload())
    ;
});

gulp.task('shop-img', function() {
    gulp.src([nodeModulesPath + 'lightbox2/dist/images/*']).pipe(gulp.dest(shopRootPath + 'images/'));

    return gulp.src(paths.shop.img)
        .pipe(gulp.dest(shopRootPath + 'img/'))
    ;
});

gulp.task('shop-watch', function() {
    livereload.listen();

    gulp.watch(paths.shop.js, ['shop-js']);
    gulp.watch(paths.shop.sass, ['shop-css']);
    gulp.watch(paths.shop.css, ['shop-css']);
    gulp.watch(paths.shop.img, ['shop-img']);
});

gulp.task('default', ['shop-js', 'shop-css', 'shop-img']);
gulp.task('watch', ['default', 'shop-watch']);
