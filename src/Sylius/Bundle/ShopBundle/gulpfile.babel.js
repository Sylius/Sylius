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
const shopRootPath = upath.joinSafe(rootPath, 'shop');
const vendorPath = upath.normalizeSafe(argv.vendorPath || '.');
const vendorShopPath = vendorPath === '.' ? '.' : upath.joinSafe(vendorPath, 'ShopBundle');
const vendorUiPath = vendorPath === '.' ? '../UiBundle/' : upath.joinSafe(vendorPath, 'UiBundle');
const nodeModulesPath = upath.normalizeSafe(argv.nodeModulesPath);

const paths = {
  shop: {
    js: [
      upath.joinSafe(nodeModulesPath, 'jquery/dist/jquery.min.js'),
      upath.joinSafe(nodeModulesPath, 'semantic-ui-css/semantic.min.js'),
      upath.joinSafe(nodeModulesPath, 'lightbox2/dist/js/lightbox.js'),
      upath.joinSafe(vendorUiPath, 'Resources/private/js/**'),
      upath.joinSafe(vendorShopPath, 'Resources/private/js/**'),
    ],
    sass: [
      upath.joinSafe(vendorUiPath, 'Resources/private/sass/**'),
      upath.joinSafe(vendorShopPath, 'Resources/private/sass/**'),
    ],
    css: [
      upath.joinSafe(nodeModulesPath, 'semantic-ui-css/semantic.min.css'),
      upath.joinSafe(nodeModulesPath, 'lightbox2/dist/css/lightbox.min.css'),
      upath.joinSafe(vendorUiPath, 'Resources/private/css/**'),
      upath.joinSafe(vendorShopPath, 'Resources/private/css/**'),
      upath.joinSafe(vendorShopPath, 'Resources/private/scss/**'),
    ],
    img: [
      upath.joinSafe(vendorUiPath, 'Resources/private/img/**'),
      upath.joinSafe(vendorShopPath, 'Resources/private/img/**'),
    ],
  },
};

const sourcePathMap = [
  {
    sourceDir: upath.relative('', upath.joinSafe(vendorShopPath, 'Resources/private')),
    destPath: '/SyliusShopBundle/',
  },
  {
    sourceDir: upath.relative('', upath.joinSafe(vendorUiPath, 'Resources/private')),
    destPath: '/SyliusUiBundle/',
  },
  {
    sourceDir: upath.relative('', nodeModulesPath),
    destPath: '/node_modules/',
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

export const buildShopJs = function buildShopJs() {
  return gulp.src(paths.shop.js, { base: './' })
    .pipe(gulpif(env !== 'prod', sourcemaps.init()))
    .pipe(concat('app.js'))
    .pipe(gulpif(env === 'prod', uglify()))
    .pipe(gulpif(env !== 'prod', sourcemaps.mapSources(mapSourcePath)))
    .pipe(gulpif(env !== 'prod', sourcemaps.write('./')))
    .pipe(gulp.dest(upath.joinSafe(shopRootPath, 'js')))
    .pipe(livereload());
};
buildShopJs.description = 'Build shop js assets.';

export const buildShopCss = function buildShopCss() {
  const copyStream = merge(
    gulp.src(upath.joinSafe(nodeModulesPath, 'semantic-ui-css/themes/**/*'))
      .pipe(gulp.dest(upath.joinSafe(shopRootPath, 'css/themes'))),
  );

  const cssStream = gulp.src(paths.shop.css, { base: './' })
    .pipe(gulpif(env !== 'prod', sourcemaps.init()))
    .pipe(concat('css-files.css'));

  const sassStream = gulp.src(paths.shop.sass, { base: './' })
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
      .pipe(gulp.dest(upath.joinSafe(shopRootPath, 'css')))
      .pipe(livereload()),
  );
};
buildShopCss.description = 'Build shop css assets.';

export const buildShopImg = function buildShopImg() {
  const copyStream = merge(
    gulp.src(upath.joinSafe(nodeModulesPath, 'lightbox2/dist/images/*'))
      .pipe(gulp.dest(upath.joinSafe(shopRootPath, 'images'))),
  );

  return merge(
    copyStream,
    gulp.src(paths.shop.img)
      .pipe(gulp.dest(upath.joinSafe(shopRootPath, 'img'))),
  );
};
buildShopImg.description = 'Build shop img assets.';

export const watchShop = function watchShop() {
  livereload.listen();

  gulp.watch(paths.shop.js, buildShopJs);
  gulp.watch(paths.shop.sass, buildShopCss);
  gulp.watch(paths.shop.css, buildShopCss);
  gulp.watch(paths.shop.img, buildShopImg);
};
watchShop.description = 'Watch shop asset sources and rebuild on changes.';

export const build = gulp.parallel(buildShopJs, buildShopCss, buildShopImg);
build.description = 'Build assets.';

export const watch = gulp.parallel(build, watchShop);
watch.description = 'Watch asset sources and rebuild on changes.';

gulp.task('shop-js', buildShopJs);
gulp.task('shop-css', buildShopCss);
gulp.task('shop-img', buildShopImg);
gulp.task('shop-watch', watchShop);

export default build;
