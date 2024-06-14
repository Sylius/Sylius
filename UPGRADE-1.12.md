# UPGRADING FROM `v1.12.17` TO `v1.12.18`

1. Due to concerns about brute forcing user's reset and verification tokens,
   the upper limit of 40 has been changed to 255 and their default length has been changed from 16 to 64.
   If you prefer shorter (or longer) tokens, you can change their length via the configuration:

   ```yml
    sylius_user:
        resources:
            _user_:
                user:
                    resetting:
                        token:
                            length: 128
                    verification:
                        token:
                            length: 12
   ```
   where `_user_` can be `admin`, `shop`, `oauth`, or your own custom user type.

1. The order token length has been parametrized and is now configurable, instead of being hardcoded to `10`.
   When not specified its default value is `64`.
   The new parameter can be set by configuration:

   ```yml
    sylius_core:
        order_token_length: 128
   ```

# UPGRADING FROM `v1.12.16` TO `v1.12.17`

1. Due to a bug that was causing wrong calculation of available stock during completing a payment [REF](https://github.com/Sylius/Sylius/issues/16160),
   The constructor of `Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener` has been modified as follows:

   ```diff
    public function __construct(
    +   private OrderItemAvailabilityCheckerInterface|AvailabilityCheckerInterface $availabilityChecker,
    -   private AvailabilityCheckerInterface $availabilityChecker,
    )
    ```

   If you have overwritten the service or its argument, check the correct functioning.

# UPGRADING FROM `v1.12.13` TO `v1.12.14`

1. The `Accept-Language` header should now correctly resolve locale codes based on RFC 4647 using Symfony's request language negotiation,
   meaning that values `en_US`, `en-US`, `en-us` etc. will all result in the `en_US` locale.

# UPGRADE FROM `v1.12.11` TO `v1.12.12`

1. The `Sylius\Component\User\Model\UserInterface` extends the `Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface`
   interface to fix the compatibility with Symfony 6.
2. The constructor of `Sylius\Component\Product\Resolver\DefaultProductVariantResolver` has been modified, a new argument has been added :
   
   ```php
    public function __construct(
        private ?ProductVariantRepositoryInterface $productVariantRepository = null,
    )
    ```

# UPGRADE FROM `v1.12.10` TO `v1.12.11`

1. Due to a bug that was causing the removal of promotion configurations for promotions [REF](https://github.com/Sylius/Sylius/issues/15201),
   The constructor of `Sylius\Bundle\CoreBundle\EventListener\ProductDeletionListener` has been modified as follows:

   ```diff
    public function __construct(
        private RequestStack $requestStack,
    +   private ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker,
    -   ProductAwareRuleUpdaterInterface ...$ruleUpdaters,
    )
    ```

   The method name has also changed from `removeProductFromPromotionRules` to `protectFromRemovingProductInUseByPromotionRule`.

   Please refrain from using ProductAwareRuleUpdaterInterface, as it will be removed in the next major release.

   * Due to the same bug, the constructor of `Sylius\Bundle\CoreBundle\EventListener\TaxonDeletionListener` has also changed:
    
      ```diff
       public function __construct(
           private SessionInterface|RequestStack $requestStackOrSession,
           private ChannelRepositoryInterface $channelRepository,
       +   private TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
           TaxonAwareRuleUpdaterInterface ...$ruleUpdaters,
       )
      ```

1. The `Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeChoicesCollectionType` only
    constructor argument has been made optional and is `null` by default, subsequently the first argument of
    `sylius.form.type.attribute_type.select.choices_collection` has been removed.

1. The default checkout resolving route pattern has been changed from `/checkout/.+` to
   `%sylius.security.shop_regex%/checkout/.+` to reduce the probability of conflicts with other routes.

1. The `src/Sylius/Bundle/AdminBundle/Resources/views/Taxon/_treeWithButtons.html.twig` template has been updated to
    implement new changing taxon's position logic. If you have overridden this template, you need to update it.
    If you want to check what has changed, you might use [this PR](https://github.com/Sylius/Sylius/pull/15272) as a reference.

# UPGRADE FROM `v1.12.9` TO `v1.12.10`

1. The `Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor` constructor has been changed:
    ```diff
    public function __construct(
        private OrderPaymentProviderInterface $orderPaymentProvider,
        private string $targetState = PaymentInterface::STATE_CART,
    +   private ?OrderPaymentsRemoverInterface $orderPaymentsRemover = null,
    +   private array $unprocessableOrderStates = [],
    )
    ```

# UPGRADE FROM `v1.12.5` TO `v1.12.8`

1. The priority of the `sylius.context.locale` tag on the `Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext` service has been changed from `256` to `32`.
    It means that this service has no longer the highest priority, and passing `Accept-Language` header on the UI won't override the locale set in the URL. If your app
    depends on this behavior, you need to change the priority of the `sylius.context.locale` tag on the `Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext` directly in your app.

# UPGRADE FROM `v1.12.4` TO `v1.12.5`

1. For routes `sylius_admin_order_shipment_ship` and `sylius_admin_order_resend_confirmation_email` the missing "/orders"
    prefix has been added. If you have been using these routes' paths directly, you need to update them.

# UPGRADE FROM `v1.12.2` TO `v1.12.4`

1. The default configuration of Symfony Messenger has changed,
   it is now separated for each transport and can be set via environmental variables:

    ```diff
        - MESSENGER_TRANSPORT_DSN=doctrine://default
        + SYLIUS_MESSENGER_TRANSPORT_MAIN_DSN=doctrine://default
        + SYLIUS_MESSENGER_TRANSPORT_MAIN_FAILED_DSN=doctrine://default?queue_name=main_failed
        + SYLIUS_MESSENGER_TRANSPORT_CATALOG_PROMOTION_REMOVAL_DSN=doctrine://default?queue_name=catalog_promotion_removal
        + SYLIUS_MESSENGER_TRANSPORT_CATALOG_PROMOTION_REMOVAL_FAILED_DSN=doctrine://default?queue_name=catalog_promotion_removal_failed
    ```

# UPGRADE FROM `v1.12.0` TO `v1.12.2`

1. All entities and their relationships have a default order by identifier if no order is specified. You can disable
   this behavior by setting the `sylius_core.order_by_identifier` parameter to `false`:
```yaml
sylius_core:
    order_by_identifier: false
```

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

10. Gaufrette is no longer used by Sylius in favour of Flysystem. If you want to use Gaufrette in your project, you need
    to set:

    ```yaml
    sylius_core:
        filesystem:
            adapter: gaufrette
    ```
    
    in your `config/packages/_sylius.yaml` file.

11. Not passing `Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface` to `Sylius\Component\Taxation\Resolver\TaxRateResolver` is deprecated since Sylius 1.12 and will be prohibited in 2.0

### Frontend toolset changes

#### Dependencies update

In `1.12` we have updated our frontend dependencies to the latest versions. This means that you might need to update your dependencies as well.
The full list of all dependencies can be found in the [package.json](./package.json) file.

Because every project is different, we cannot provide a list of all changes that might be needed. However, we have prepared a short list of fixes for the most common issues.

We updated gulp-sass plugin as well as the sass implementation we use to be compatible with most installation
([node-sass](https://sass-lang.com/blog/libsass-is-deprecated) is deprecated and incompatible with many systems).
Therefore, you need to update your code to follow this change.

**NOTE!** `yarn build` is not used to build gulp anymore, and its default behavior is to build assets using Webpack. If you want to build assets using Gulp run `yarn gulp` instead.

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

#### For plugin developers - `use_webpack` Twig's global

We have introduced a new `use_webpack` global for Twig templates. It allows you to check if Webpack is declared as a build tool
to dynamically serve assets from the correct directory.

**Example:**
```html
<div class="column">
    <a href="{{ path('sylius_shop_homepage') }}">
        {% if use_webpack %}
            <img src="{{ asset('build/shop/images/logo.png', 'shop') }}" alt="Sylius logo" class="ui small image" />
        {% else %}
            <img src="{{ asset('assets/shop/img/logo.png') }}" alt="Sylius logo" class="ui small image" />
        {% endif %}
    </a>
</div>
```

## Testing suite

#### Behat changes

As the default mailer integration has been changed from Swiftmailer to Symfony Mailer, the following changes have to be applied.

1. Remove the `config/packages/test/swiftmailer.yaml` file
2. Add a `config/packages/test/mailer.yaml` file with:
   ```yml
   framework:
       mailer:
           dsn: 'null://null'
       cache:
           pools:
               test.mailer_pool:
                   adapter: cache.adapter.filesystem
   ```
3. Change all occurrences of `sylius.behat.context.hook.email_spool` to `sylius.behat.context.hook.mailer`.

Due to the changes in Symfony's session handling you might need to add the `sylius.behat.context.hook.session` context to your suites.
