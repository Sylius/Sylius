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

var rootPath = argv.rootPath;
var uiRootPath = rootPath + 'ui/';
var resourcePath = argv.resourcePath || '';
var nodeModulesPath = argv.nodeModulesPath;

var paths = {
    ui: {
        js: [
            resourcePath + 'Resources/private/js/**'
        ],
        sass: [
            resourcePath + 'Resources/private/sass/**'
        ],
        css: [
            resourcePath + 'Resources/private/css/**'
        ],
        img: [
            resourcePath + 'Resources/private/img/**'
        ]
    },
    nodeModules: {
        js: [
            nodeModulesPath + 'jquery/dist/jquery.min.js',
            nodeModulesPath + 'semantic-ui-css/semantic.min.js'
        ],
        css: [
            nodeModulesPath + 'semantic-ui-css/semantic.min.css'
        ]
    }
};

gulp.task('ui-js', function () {
    var nodeModulesJsStream = gulp.src(paths.nodeModules.js)
            .pipe(concat('node-modules.js'))
        ;

    var uiJsStream = gulp.src(paths.ui.js)
            .pipe(concat('ui.js'))
        ;

    return merge(nodeModulesJsStream, uiJsStream)
        .pipe(order(['node-modules.js', 'ui.js']))
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(uiRootPath + 'js/'))
    ;
});

gulp.task('ui-css', function() {
    gulp.src([nodeModulesPath+'semantic-ui-css/themes/**/*']).pipe(gulp.dest(uiRootPath + 'css/themes/'));

    var nodeModulesCssStream = gulp.src(paths.nodeModules.css)
            .pipe(concat('node-modules-css-files.css'))
        ;

    var cssStream = gulp.src(paths.ui.css)
            .pipe(concat('css-files.css'))
        ;

    var sassStream = gulp.src(paths.ui.sass)
            .pipe(sass())
            .pipe(concat('sass-files.scss'))
        ;

    return merge(nodeModulesCssStream, cssStream, sassStream)
        .pipe(order(['node-modules-css-files.css', 'css-files.css', 'sass-files.scss']))
        .pipe(concat('style.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(uiRootPath + 'css/'))
        .pipe(livereload())
    ;
});

gulp.task('ui-img', function() {
    return gulp.src(paths.ui.img)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(uiRootPath + 'img/'))
    ;
});

gulp.task('default', ['ui-js', 'ui-css', 'ui-img']);
