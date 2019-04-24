import concat from 'gulp-concat';
import gulp from 'gulp';
import gulpif from 'gulp-if';
import livereload from 'gulp-livereload';
import uglify from 'gulp-uglify';
import upath from 'upath';
import sass from 'gulp-sass';
import merge from 'merge-stream';

const env = process.env.GULP_ENV;
const options = {
  minify: env === 'prod',
};

const appRootPath = '../public/assets/app';
const nodeModulesPath = '../node_modules';
const paths = {
  app: {
    js: [
      upath.joinSafe(nodeModulesPath, 'jquery/dist/jquery.min.js'),
      'js/**',
    ],
    css: [
      'css/**',
    ],
    scss: [
      'scss/**',
    ],
    img: [
      'image/**',
    ],
  },
};

export const buildAppJs = function buildAppJs() {
  return gulp.src(paths.app.js)
    .pipe(concat('app.js'))
    .pipe(gulpif(options.minify, uglify()))
    .pipe(gulp.dest(upath.joinSafe(appRootPath, 'js')));
};
buildAppJs.description = 'Build app js assets.';

export const buildAppCss = function buildAppCss() {
  const scssStream = gulp.src(paths.app.scss)
    .pipe(sass())
    .pipe(concat('scss-files.scss'));

  const cssStream = gulp.src(paths.app.css)
    .pipe(concat('css-files.css'));

  return merge(scssStream, cssStream)
    .pipe(concat('app.css'))
    .pipe(gulpif(options.minify, uglify()))
    .pipe(gulp.dest(upath.joinSafe(appRootPath, 'css')));
};
buildAppCss.description = 'Build app css assets.';

export const buildAppImg = function buildAppImg() {
  return gulp.src(paths.app.img).pipe(gulp.dest(upath.joinSafe(appRootPath, 'img')));
};
buildAppImg.description = 'Build app img assets.';

export const watchApp = function watchApp() {
  livereload.listen();

  gulp.watch(paths.app.js, buildAppJs);
  gulp.watch(paths.app.css, buildAppCss);
  gulp.watch(paths.app.img, buildAppImg);
};
watchApp.description = 'Watch app asset sources and rebuild on changes.';

export const build = gulp.parallel(buildAppJs, buildAppCss, buildAppImg);
build.description = 'Build assets.';

export const watch = gulp.parallel(build, watchApp);
watch.description = 'Watch asset sources and rebuild on changes.';

gulp.task('app-js', buildAppJs);
gulp.task('app-css', buildAppCss);
gulp.task('app-img', buildAppImg);
gulp.task('app-watch', watchApp);

export default build;
