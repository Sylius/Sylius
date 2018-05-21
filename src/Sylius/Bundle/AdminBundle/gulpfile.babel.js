import concat from 'gulp-concat';
import gulp from 'gulp';
import gulpif from 'gulp-if';
import livereload from 'gulp-livereload';
import merge from 'merge-stream';
import order from 'gulp-order';
import sass from 'gulp-sass';
import sourcemaps from 'gulp-sourcemaps';
import uglify from 'gulp-uglify';
import uglifycss from 'gulp-uglifycss';
import upath from 'upath';
import yargs from 'yargs';

const { argv } = yargs
  .options({
    rootPath: {
      description: '<path> path to web assets directory',
      type: 'string',
      requiresArg: true,
      required: true,
    },
    vendorPath: {
      description: '<path> path to vendor directory',
      type: 'string',
      requiresArg: true,
      required: false,
    },
    nodeModulesPath: {
      description: '<path> path to node_modules directory',
      type: 'string',
      requiresArg: true,
      required: true,
    },
  });

const env = process.env.GULP_ENV;
const rootPath = upath.normalizeSafe(argv.rootPath);
const adminRootPath = upath.joinSafe(rootPath, 'admin');
const vendorPath = upath.normalizeSafe(argv.vendorPath || '.');
const vendorAdminPath = vendorPath === '.' ? '.' : upath.joinSafe(vendorPath, 'AdminBundle');
const vendorUiPath = vendorPath === '.' ? '../UiBundle/' : upath.joinSafe(vendorPath, 'UiBundle');
const nodeModulesPath = upath.normalizeSafe(argv.nodeModulesPath);

const paths = {
  admin: {
    js: [
      upath.joinSafe(nodeModulesPath, 'jquery/dist/jquery.min.js'),
      upath.joinSafe(nodeModulesPath, 'semantic-ui-css/semantic.min.js'),
      upath.joinSafe(vendorUiPath, 'Resources/private/js/**'),
      upath.joinSafe(vendorAdminPath, 'Resources/private/js/**'),
    ],
    sass: [
      upath.joinSafe(vendorUiPath, 'Resources/private/sass/**'),
      upath.joinSafe(vendorAdminPath, 'Resources/private/sass/**'),
    ],
    css: [
      upath.joinSafe(nodeModulesPath, 'semantic-ui-css/semantic.min.css'),
      upath.joinSafe(vendorUiPath, 'Resources/private/css/**'),
      upath.joinSafe(vendorAdminPath, 'Resources/private/css/**'),
    ],
    img: [
      upath.joinSafe(vendorUiPath, 'Resources/private/img/**'),
      upath.joinSafe(vendorAdminPath, 'Resources/private/img/**'),
    ],
  },
};

const sourcePathMap = [
  {
    sourceDir: upath.relative('', upath.joinSafe(vendorAdminPath, 'Resources/private/')),
    destPath: '/SyliusAdminBundle',
  },
  {
    sourceDir: upath.relative('', upath.joinSafe(vendorUiPath, 'Resources/private/')),
    destPath: '/SyliusUiBundle',
  },
  {
    sourceDir: upath.relative('', nodeModulesPath),
    destPath: '/node_modules',
  },
];

const mapSourcePath = function mapSourcePath(sourcePath /* , file */) {
  const match = sourcePathMap.find(({ sourceDir }) => (
    sourcePath.substring(0, sourceDir.length) === sourceDir
  ));

  if (!match) {
    return sourcePath;
  }

  const { sourceDir, destPath } = match;

  return upath.joinSafe(destPath, sourcePath.substring(sourceDir.length));
};

export const buildAdminJs = function buildAdminJs() {
  return gulp.src(paths.admin.js, { base: './' })
    .pipe(gulpif(env !== 'prod', sourcemaps.init()))
    .pipe(concat('app.js'))
    .pipe(gulpif(env === 'prod', uglify()))
    .pipe(gulpif(env !== 'prod', sourcemaps.mapSources(mapSourcePath)))
    .pipe(gulpif(env !== 'prod', sourcemaps.write('./')))
    .pipe(gulp.dest(upath.joinSafe(adminRootPath, 'js')))
    .pipe(livereload());
};
buildAdminJs.description = 'Build admin js assets.';

export const buildAdminCss = function buildAdminCss() {
  const copyStream = merge(
    gulp.src(upath.joinSafe(nodeModulesPath, 'semantic-ui-css/themes/**/*'))
      .pipe(gulp.dest(upath.joinSafe(adminRootPath, 'css/themes'))),
  );

  const cssStream = gulp.src(paths.admin.css, { base: './' })
    .pipe(gulpif(env !== 'prod', sourcemaps.init()))
    .pipe(concat('css-files.css'));

  const sassStream = gulp.src(paths.admin.sass, { base: './' })
    .pipe(gulpif(env !== 'prod', sourcemaps.init()))
    .pipe(sass())
    .pipe(concat('sass-files.scss'));

  return merge(
    copyStream,
    merge(cssStream, sassStream)
      .pipe(order(['css-files.css', 'sass-files.scss']))
      .pipe(concat('style.css'))
      .pipe(gulpif(env === 'prod', uglifycss()))
      .pipe(gulpif(env !== 'prod', sourcemaps.mapSources(mapSourcePath)))
      .pipe(gulpif(env !== 'prod', sourcemaps.write('./')))
      .pipe(gulp.dest(upath.joinSafe(adminRootPath, 'css')))
      .pipe(livereload()),
  );
};
buildAdminCss.description = 'Build admin css assets.';

export const buildAdminImg = function buildAdminImg() {
  return gulp.src(paths.admin.img)
    .pipe(gulp.dest(upath.joinSafe(adminRootPath, 'img')));
};
buildAdminImg.description = 'Build admin img assets.';

export const watchAdmin = function watchAdmin() {
  livereload.listen();

  gulp.watch(paths.admin.js, buildAdminJs);
  gulp.watch(paths.admin.sass, buildAdminCss);
  gulp.watch(paths.admin.css, buildAdminCss);
  gulp.watch(paths.admin.img, buildAdminImg);
};
watchAdmin.description = 'Watch admin asset sources and rebuild on changes.';

export const build = gulp.parallel(buildAdminJs, buildAdminCss, buildAdminImg);
build.description = 'Build assets.';

export const watch = gulp.parallel(build, watchAdmin);
watch.description = 'Watch asset sources and rebuild on changes.';

gulp.task('admin-js', buildAdminJs);
gulp.task('admin-css', buildAdminCss);
gulp.task('admin-img', buildAdminImg);
gulp.task('admin-watch', watchAdmin);

export default build;
