# UPGRADE FROM `v1.11.X` TO `v1.12.0`

## Main update

1. Service `sylius.twig.extension.taxes` has been deprecated. Use methods `getTaxExcludedTotal` and `getTaxIncludedTotal` 
   from `Sylius\Component\Core\Model\Order` instead.

2. Both `getCreatedByGuest` and `setCreatedByGuest` methods were deprecated on `\Sylius\Component\Core\Model\Order`. 
Please use `isCreatedByGuest` instead of the first one. The latter is a part of the `setCustomerWithAuthorization` logic 
and should be used only this way.

3. Due to refactoring constructor has been changed in service `src/Sylius/Bundle/ShopBundle/EventListener/OrderIntegrityChecker.php`:
    ```diff
      public function __construct(
        private RouterInterface $router,
        - private OrderProcessorInterface $orderProcessor,
        private ObjectManager $manager
        + private OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker 
      )
    ```

4. To allow administrator reset their password, add in `config/packages/security.yaml` file the following entry
   ```yaml
           - { path: "%sylius.security.admin_regex%/forgotten-password", role: IS_AUTHENTICATED_ANONYMOUSLY }
   ```
   above
   ```yaml
           - { path: "%sylius.security.admin_regex%", role: ROLE_ADMINISTRATION_ACCESS }
   ```

5. It is worth noticing that, that the [following services](https://github.com/Sylius/Sylius/blob/1.12/src/Sylius/Bundle/CoreBundle/Resources/config/test_services.xml) 
are now included in every env starting with `test` keyword. If you wish to not have them, either you need to rename your env to not start 
with test or remove these services with complier pass.

6. Remove the `success_handler` node from shop's firewall
    ```diff
    logout:
        path: sylius_shop_logout
        target: sylius_shop_login
        invalidate_session: false
    -    success_handler: sylius.handler.shop_user_logout
    ```

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
   ```diff
   - "rollup": "^0.60.2",
   + "rollup": "^0.66.2",
   ```
   ```diff
   - "rollup-plugin-uglify": "^4.0.0",
   + "rollup-plugin-uglify": "^6.0.2",
   ```
    Please visit [3.x Migration Guide](https://www.chartjs.org/docs/latest/getting-started/v3-migration.html) for more information.
