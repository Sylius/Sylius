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

6. The following listeners have been removed as they are not used anymore:
   - `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionFailedListener`
   - `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionUpdateFailedMessageListener`

7. The `Sylius\Component\Promotion\Event\CatalogPromotionFailed` has been removed as it is not used anymore.

8. Due to updating to Symfony 6 security file was changed to use the updated security system so you need to adjust your `config/packages/security.yaml` file:
    
   ```diff
   security:
   -   always_authenticate_before_granting: true
   +   enable_authenticator_manager: true
   ```

   and you need to adjust all of your firewalls like that:

   ```diff
   admin:
       # ...
       form_login:
           # ...
   -       csrf_token_generator: security.csrf.token_manager
   +       enable_csrf: true
           # ...
   new_api_admin_user:
       # ...
   -   anonymous: true
   +   entry_point: jwt
       # ...
   -   guard:
   -       authenticators:
   -           # ...
   +   jwt: true
   shop:
       logout:
       path: sylius_shop_logout
   -   target: sylius_shop_login
   +   target: sylius_shop_homepage
       invalidate_session: false
   -   success_handler: sylius.handler.shop_user_logout
   ```
   
    and also you need to adjust all of your access_control like that:
    
   ```diff
   - - { path: "%sylius.security.admin_regex%/forgotten-password", role: IS_AUTHENTICATED_ANONYMOUSLY } 
   + - { path: "%sylius.security.admin_regex%/forgotten-password", role: PUBLIC_ACCESS }
   ```

9. Passing a `Gaufrette\Filesystem` to `Sylius\Component\Core\Uploader\ImageUploader` constructor is deprecated since
Sylius 1.12 and will be prohibited in 2.0. Use `Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter` instead.

### Frontend toolset changes

#### Dependencies update

In `1.12` we have updated our frontend dependencies to the latest versions. This means that you might need to update your dependencies as well.
The full list of all dependencies can be found in the [package.json](./package.json) file.

Due to a fact every project is different, we cannot provide a list of all changes that might be needed. However, we have prepared a list of the most common changes that might be needed.

We updated gulp-sass plugin as well as the sass implementation we use to be compatible with most installation
([node-sass](https://sass-lang.com/blog/libsass-is-deprecated) is deprecated and incompatible with many systems).
Therefore you need to update your code to follow this change.

1. Follow [this guide](https://github.com/dlmanning/gulp-sass/tree/master#migrating-to-version-5) to upgrade your
   code when using gulp-sass this is an example:
   ```diff
   - import sass from 'gulp-sass';
   + import gulpSass from 'gulp-sass';
   + import realSass from 'sass';
   + const sass = gulpSass(realSass);
   ```

2. Library chart.js lib has been upgraded from 2.9.3 to 3.7.1. Adjust your package.json as follows:

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

Example changes we made in our codebase to adjust it to the new version of dependencies might be found [here](https://github.com/Sylius/Sylius/pull/14319/files).
Remember, when you face any issues while updating your dependencies you might ask for help in our [Slack](https://sylius-devs.slack.com/) channel.

#### Webpack becomes our default build tool

`1.12` comes with a long-awaited change - Webpack becomes our default build tool. 

If you want to stay with Gulp, you can do it by following the steps below:

1. Go to `config/packages/_sylius.yaml` file and add the following line:
   ```yaml
   sylius_ui:
      use_webpack: false
   ```

If you decide to use Webpack, you need to follow the steps below:

1. Make sure you have latest js dependencies installed (you can compare your `package.json` file with the one from `1.12`).
2. Make sure you have `webpack.config.js` file, if not, you can copy it from [Sylius/Sylius-Standard](https://github.com/Sylius/Sylius-Standard) repository.
3. Run the following command
   ```bash
   yarn encore dev
   ```
   
**Remember!**  Every project is different, so you might need to adjust your code to work with Webpack.
