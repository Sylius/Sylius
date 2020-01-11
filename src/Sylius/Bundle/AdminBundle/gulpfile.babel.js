import { rollup } from 'rollup';
import { uglify } from 'rollup-plugin-uglify';
import babel from 'rollup-plugin-babel';
import commonjs from 'rollup-plugin-commonjs';
import concat from 'gulp-concat';
import dedent from 'dedent';
import gulp from 'gulp';
import gulpif from 'gulp-if';
import inject from 'rollup-plugin-inject';
import livereload from 'gulp-livereload';
import merge from 'merge-stream';
import order from 'gulp-order';
import resolve from 'rollup-plugin-node-resolve';
import sass from 'gulp-sass';
import sourcemaps from 'gulp-sourcemaps';
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
const options = {
  minify: env === 'prod',
  sourcemaps: env !== 'prod',
};

const rootPath = upath.normalizeSafe(argv.rootPath);
const adminRootPath = upath.joinSafe(rootPath, 'admin');
const vendorPath = upath.normalizeSafe(argv.vendorPath || '.');
const vendorAdminPath = vendorPath === '.' ? '.' : upath.joinSafe(vendorPath, 'AdminBundle');
const vendorUiPath = vendorPath === '.' ? '../UiBundle/' : upath.joinSafe(vendorPath, 'UiBundle');
const nodeModulesPath = upath.normalizeSafe(argv.nodeModulesPath);

const paths = {
  admin: {
    js: [
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

const mapSourcePath = function mapSourcePath(sourcePath) {
  const match = sourcePathMap.find(({ sourceDir }) => (
    sourcePath.substring(0, sourceDir.length) === sourceDir
  ));

  if (!match) {
    return sourcePath;
  }

  const { sourceDir, destPath } = match;

  return upath.joinSafe(destPath, sourcePath.substring(sourceDir.length));
};

export const buildAdminJs = async function buildAdminJs() {
  const bundle = await rollup({
    input: upath.joinSafe(vendorAdminPath, 'Resources/private/js/app.js'),
    plugins: [
      {
        name: 'shim-app',

        transform(code, id) {
          if (upath.relative('', id) === upath.relative('', upath.joinSafe(vendorAdminPath, 'Resources/private/js/app.js'))) {
            return {
              code: dedent`
                import './shim/shim-polyfill';
                import './shim/shim-jquery';
                import './shim/shim-semantic-ui';

                ${code}
              `,
              map: null,
            };
          }

          return undefined;
        },
      },
      inject({
        include: `${nodeModulesPath}/**`,
        modules: {
          $: 'jquery',
          jQuery: 'jquery',
        },
      }),
      resolve({
        jail: upath.resolve(nodeModulesPath),
      }),
      commonjs({
        include: `${nodeModulesPath}/**`,
      }),
      babel({
        babelrc: false,
        exclude: `${nodeModulesPath}/**`,
        presets: [
          ['env', {
            targets: {
              browsers: [
                'last 2 versions',
                'Firefox ESR',
                'IE >= 9',
                'Android >= 4.0',
                'iOS >= 7',
              ],
            },
            modules: false,
            exclude: [
              'transform-async-to-generator',
              'transform-regenerator',
            ],
            useBuiltIns: true,
          }],
        ],
        plugins: [
          ['external-helpers'],
          ['fast-async'],
          ['module-resolver', {
            alias: {
              'sylius/ui': upath.relative('', upath.joinSafe(vendorUiPath, 'Resources/private/js')),
            },
          }],
          ['transform-object-rest-spread', {
            useBuiltIns: false,
          }],
        ],
      }),
      options.minify && uglify(),
    ],
    treeshake: false,
  });

  await bundle.write({
    file: upath.joinSafe(adminRootPath, 'js/app.js'),
    format: 'iife',
    sourcemap: options.sourcemaps,
  });
};
buildAdminJs.description = 'Build admin js assets.';

export const buildAdminCss = function buildAdminCss() {
  const copyStream = merge(
    gulp.src(upath.joinSafe(nodeModulesPath, 'semantic-ui-css/themes/**/*'))
      .pipe(gulp.dest(upath.joinSafe(adminRootPath, 'css/themes'))),
  );

  const cssStream = gulp.src(paths.admin.css, { base: './' })
    .pipe(gulpif(options.sourcemaps, sourcemaps.init()))
    .pipe(concat('css-files.css'));

  const sassStream = gulp.src(paths.admin.sass, { base: './' })
    .pipe(gulpif(options.sourcemaps, sourcemaps.init()))
    .pipe(sass())
    .pipe(concat('sass-files.scss'));

  return merge(
    copyStream,
    merge(cssStream, sassStream)
      .pipe(order(['css-files.css', 'sass-files.scss']))
      .pipe(concat('style.css'))
      .pipe(gulpif(options.minify, uglifycss()))
      .pipe(gulpif(options.sourcemaps, sourcemaps.mapSources(mapSourcePath)))
      .pipe(gulpif(options.sourcemaps, sourcemaps.write('./')))
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
