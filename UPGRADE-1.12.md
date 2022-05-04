# UPGRADE FROM `v1.11.X` TO `v1.12.0`

## Main update

### Asset management changes

We updated gulp-sass plugin as well as the sass implementation we use to be compatible with most installation
([node-sass](https://sass-lang.com/blog/libsass-is-deprecated) is deprecated and incompatible with many systems).
Therefore you need to update your code to follow this change.

1. Change the gulp-sass version you are using to `^5.1.0` (package.json file)
   ```diff
   - "gulp-sass": "^4.0.1",
   + "gulp-sass": "^5.1.0",
   ```
2. Add sass to your package.json:
   ```diff
   + "sass": "^1.48.0",
   ```
3. Follow [this guide](https://github.com/dlmanning/gulp-sass/tree/master#migrating-to-version-5) to upgrade your
   code when using gulp-sass this is an example:
   ```diff
   - import sass from 'gulp-sass';
   + import gulpSass from 'gulp-sass';
   + import realSass from 'sass';
   + const sass = gulpSass(realSass);
   ```

4. Library chart.js lib has been upgraded from 2.9.3 to 3.7.1. Adjust your package.json as follows:

   ```diff
   - "chart.js": "^2.9.3",
   + "chart.js": "^3.7.1", 
   ```
   
    Please visit [3.x Migration Guide](https://www.chartjs.org/docs/latest/getting-started/v3-migration.html) for more information.
