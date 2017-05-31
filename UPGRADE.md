# UPGRADE FROM 1.0.0-beta.2 to 1.0.0

## Packages:

### PayumBundle

* Constructor of `CapturePaymentAction` now takes a `PaymentDescriptionProviderInterface` as first argument. This allows granular customisation of the payment description.

### Core / CoreBundle

* The following serialization configuration was moved from CoreBundle/Resources/config/app/config.yml to AdminApiBundle/Resources/config/app/config.yml

  ```yaml
      jms_serializer:
         metadata:
             directories:
                 sylius-core:
                     namespace_prefix: "Sylius\\Component\\Core"
                     path: "@SyliusCoreBundle/Resources/config/serializer"
  ```
  to
  ```yaml
      jms_serializer:
         metadata:
             directories:
                 sylius-core:
                     namespace_prefix: "Sylius\\Component\\Core"
                     path: "@SyliusAdminApiBundle/Resources/config/serializer"
  ```
* Relations in serializations files were moved from Sylius bundles to SyliusAdminApiBundle

  Example of relation for SyliusOrderBundle/Resources/config/serializer/Model.OrderItem.yml
  ```yaml
      relations:
        -   rel: order
            href:
                route: sylius_admin_api_order_show
                parameters:
                    id: expr(object.getOrder().getId())
                    version: 1
            exclusion:
                groups: [Default, Detailed, DetailedCart]
  ```

* The following serialization configurations files were moved:

  * from `SyliusCoreBundle/Resources/config/serializer/Model.AdminUser.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.AdminUser.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Channel.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Channel.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.ChannelPricing.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.ChannelPricing.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Customer.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Customer.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Image.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Image.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Order.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Order.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.OrderItem.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.OrderItem.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.OrderItemUnit.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.OrderItemUnit.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Payment.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Payment.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.PaymentMethod.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.PaymentMethod.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Product.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Product.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.ProductImage.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.ProductImage.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.ProductTaxon.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.ProductTaxon.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.ProductVariant.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.ProductVariant.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Promotion.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Promotion.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.PromotionCoupon.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.PromotionCoupon.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Property.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Property.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Shipment.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Shipment.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.ShippingMethod.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.ShippingMethod.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.ShopUser.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.ShopUser.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.TaxRate.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.TaxRate.yml`
  * from `SyliusCoreBundle/Resources/config/serializer/Model.Taxon.yml` to `SyliusAdminApiBundle/Resources/config/serializer/Model.Taxon.yml`

# UPGRADE FROM 1.0.0-beta.1 to 1.0.0-beta.2

* Bundles, container extensions and bundles configurations were made final and can't be extended anymore, follow Symfony
  best practices and do not extend them.

## Packages:

### Addressing / AddressingBundle

### AdminBundle

* Route `sylius_admin_order_shipment_ship` has been added to have specific endpoint only for updating via HTTP PUT method and `sylius_admin_partial_shipment_ship` route is only for rendering the form.

* Route `sylius_admin_address_log_entry_index` was renamed to `sylius_admin_partial_log_entry_index`.

### AdminApiBundle (former ApiBundle)

* Bundle was renamed from `ApiBundle` to `AdminApiBundle`. Routing and config was changed from `sylius_api` to `sylius_admin_api`.

* Change the import path of your API config from `@SyliusApiBundle/Resources/config/app/config.yml` to `@SyliusAdminApiBundle/Resources/config/app/config.yml` (in file `app/config/config.yml`).

* Change the import path of your API routing from `src/Sylius/Bundle/ApiBundle/Resources/config/routing/main.yml` to `src/Sylius/Bundle/AdminApiBundle/Resources/config/routing.yml`. API became versioned, so you need to prefix them accordingly (e.g. `/api/customer` -> `/api/v1/customer`).

* Routing for the following resources has been changed to use code instead of id:

    * `Products`
    * `Product Variants`
    * `Taxons`

  You can bring back previous configuration by overriding current routing with your definition.

* Routing for the following resources has been removed and replaced with the auto generated routing:

    * `Channels`, which are now resolved by code instead of id and only index and show endpoint are available.
    * `Countries`, which are now resolved by code instead of id.
    * `Locales`, which  are now resolved by code instead of id.
    * `Product Attributes`, which are now by code instead of id and only index and show endpoint are available.
    * `Product Options`, which are now resolved by code instead of id and only index and show endpoint are available.
    * `Promotions`, which are now resolved by code instead of id.
    * `Promotions Coupons`, which are now resolved by code instead of id and only index and show endpoint are available.
    * `Shipping Categories`, which are now resolved by code instead of id.
    * `Tax Categories`, which are now resolved by code instead of id.
    * `Tax Rates`, which have now only index and show endpoint available.
    * `Payment Methods`, which have now only show endpoint available.

  You can bring back previous configuration by overriding current routing with your definition.

### Attribute / AttributeBundle

* `AttributeValue::$localeCode` property was added to make it translatable. Now, every attribute value has a locale code
  to be displayed properly in different locales. All attribute values are migrated to the new concept with migration
  `Version20170109143010`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7219) if you have any problems with upgrade.

* `Sylius\Component\Attribute\Repository\AttributeRepositoryInterface` and its implementations were removed due to not being
  used anymore. You can bring back missing `findByName` method by copying it from Git history to your codebase.

### Channel / ChannelBundle

### Core / CoreBundle

* `ImageUniqueCode` and `ImageUniqueCodeValidator` were deleted and replaced by `UniqueWithinCollectionConstraint` and
  `UniqueWithinCollectionConstraintValidator` from `ResourceBundle`.
  To use it replace name of constraint in constraint mapping file from `Sylius\Bundle\CoreBundle\Validator\Constraints\ImageUniqueCode`
  to `Sylius\Bundle\ResourceBundle\Validator\Constraints\UniqueWithinCollectionConstraint`

* Renamed ``getLastNewPayment()`` on ``OrderInterface`` to ``getLastPayment($state)``, where ``$state`` is target last payment state.
  Every ``getLastNewPayment()`` method should be replaced with ``getLastPayment(PaymentInterface::STATE_NEW)``.

* `Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor` and `Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver`
  have become a zone scope aware. From now, only zones with scope `shipping` or `all` will be considered in
  `Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver` and a scope `tax` or `all` is required by
  `Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor`. A migration file has been prepared which fill in `all`
  as scope for zones that didn't have it specified, so they will be resolved by new implementation.

* State resolvers have been made final. In order to change theirs behavior please decorate them or provide your own implementation.

* `Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RoutingRepositoryPass` was removed, implement it yourself.

* Method `createQueryBuilderByChannelAndTaxonSlug` from `Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepositoryInterface`
  was renamed to `createShopListQueryBuilder` and receives taxon object instead of taxon slug string as the second parameter.

* `Sylius\Bundle\CoreBundle\Test\MySqlDriver` and `Sylius\Bundle\CoreBundle\Test\PgSqlDriver` were removed in favour of
  `dama/doctrine-test-bundle` package.

* `Sylius\Component\Core\Test\Services\RandomInvoiceNumberGenerator` was moved to `Sylius\Component\Core\Payment\RandomInvoiceNumberGenerator`,
  change your usages accordingly.

* `Sylius\Component\Core\Payment\RandomInvoiceNumberGenerator` and `Sylius\Component\Core\Payment\IdBasedInvoiceNumberGenerator`
  were made final, use decoration and implement `Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface` instead of
  extending them.

* `Sylius\Component\Core\Currency\SessionBasedCurrencyStorage` was removed and replaced by more generic `Sylius\Component\Core\Currency\CurrencyStorage`

* The following classes were removed due to being no longer used in current implementation:

  * `Sylius\Bundle\CoreBundle\Handler\ShopCurrencyChangeHandler`
  * `Sylius\Component\Core\Currency\Handler\CompositeCurrencyChangeHandler`
  * `Sylius\Component\Core\Currency\Handler\CurrencyChangeHandlerInterface`
  * `Sylius\Component\Core\Provider\ChannelBasedCurrencyProvider`
  * `Sylius\Component\Core\SyliusCurrencyEvents`

* The following classes and interfaces was removed due to changes in locales handling (prepending them in URLs):

  * `Sylius\Bundle\CoreBundle\Handler\CartLocaleChangeHandler`
  * `Sylius\Bundle\CoreBundle\Handler\ShopLocaleChangeHandler`
  * `Sylius\Component\Core\Locale\Handler\CompositeLocaleChangeHandler`
  * `Sylius\Component\Core\Locale\Handler\LocaleChangeHandlerInterface`

* `Sylius\Component\Core\Repository\ProductRepositoryInterface` definition changed.

  * `findLatestByChannel(ChannelInterface $channel, int $count)` was changed to
    `findLatestByChannel(ChannelInterface $channel, string $locale, int $count)`.
    Please provide your current locale to fetch products together with their translations.

  * `findOneBySlugAndChannel(string $slug, ChannelInterface $channel)` was changed to
    `findOneByChannelAndSlug(ChannelInterface $channel, string $locale, string $slug)`.
    Please provide your current locale to fetch product together with its translations.

  * `findOneBySlug(string $slug)` was removed and replaced with more specific
    `findOneByChannelAndSlug(ChannelInterface $channel, string $locale, string $slug)`.

* Added `Payment::$gatewayConfig` property (with corresponding getter and setter) to allow dynamic gateways. Use it instead of old `Payment::$gateway` property.

* The following methods were added to `Sylius\Component\Core\Repository\OrderRepositoryInterface`:

  * `findCartForSummary($id): ?OrderInterface`
  * `findCartForAddressing($id): ?OrderInterface`
  * `findCartForSelectingShipping($id): ?OrderInterface`
  * `findCartForSelectingPayment($id): ?OrderInterface`

* `Channel` relation was removed from `ChannelPricing` model. `ChannelPricing::$channelCode` should be used instead.

* The following classes were moved:

  * from `Sylius\Bundle\CoreBundle\EmailManager\ShipmentEmailManager` to `Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManager`
  * from `Sylius\Bundle\CoreBundle\EmailManager\ShipmentEmailManagerInterface` to `Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface`
  * from `Sylius\Bundle\CoreBundle\EmailManager\ContactEmailManager` to `Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManager`
  * from `Sylius\Bundle\CoreBundle\EmailManager\ContactEmailManagerInterface` to `Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface`
  * from `Sylius\Bundle\CoreBundle\EmailManager\OrderEmailManager` to `Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManager`
  * from `Sylius\Bundle\CoreBundle\EmailManager\OrderEmailManagerInterface` to `Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface`
  * from `Sylius\Bundle\CoreBundle\EventListener\UserMailerListener` to `Sylius\Bundle\ShopBundle\EventListener\UserMailerListener`

* The following email templates were moved:

  * from `SyliusCoreBundle:Email:shipmentConfirmation.html.twig` to `SyliusAdminBundle:Email:shipmentConfirmation.html.twig`
  * from `SyliusCoreBundle:Email:contactRequest.html.twig` to `SyliusShopBundle:Email:contactRequest.html.twig`
  * from `SyliusCoreBundle:Email:orderConfirmation.html.twig` to `SyliusShopBundle:Email:orderConfirmation.html.twig`
  * from `SyliusCoreBundle:Email:userRegistration.html.twig` to `SyliusShopBundle:Email:userRegistration.html.twig`
  * from `SyliusCoreBundle:Email:passwordReset.html.twig` to `SyliusShopBundle:Email:passwordReset.html.twig`
  * from `SyliusCoreBundle:Email:verification.html.twig` to `SyliusShopBundle:Email:verification.html.twig`

* Removed class `Sylius\Bundle\CoreBundle\Form\Type\ProductTaxonChoiceType`, use `Sylius\Bundle\CoreBundle\Form\Type\Taxon\ProductTaxonAutocompleteChoiceType` instead.

* Removed `Sylius\Bundle\CoreBundle\Form\Type\Promotion\PromotionConfigurationType` class as it has no behaviour and is not used.

* Removed `filterProductTaxonsByTaxon` method from `ProductTaxonAwareInterface`, added `getTaxons` and `hasTaxon` methods.
  If you used the removed method to determine whether product belongs to a given taxon, use `hasTaxon` instead.

* Removed `Sylius\Component\Core\Promotion\Action\ChannelBasedPromotionActionCommandInterface` and
  `Sylius\Component\Core\Promotion\Checker\Rule\ChannelBasedRuleCheckerInterface` interfaces together with
  `Sylius\Bundle\CoreBundle\Form\EventSubscriber\BuildChannelBasedPromotionActionFormSubscriber` and
  `Sylius\Bundle\CoreBundle\Form\EventSubscriber\BuildChannelBasedPromotionRuleFormSubscriber` event subscribers,
  which magically resolved channel-based configurations, look at `ChannelBased*Type` to implement your own channel-based configs.

* Services tagged with `sylius.promotion_action` and `sylius.promotion_rule_checker` must include `form-type` parameter
  being the FQCN of configuration type.

* Removed class `Sylius\Component\Core\TokenAssigner\UniqueTokenGenerator`, use `Sylius\Component\Resource\Generator\RandomnessGenerator` instead.

### Currency / CurrencyBundle

* The following classes were removed due to being no longer used in current implementation:

  * `Sylius\Component\Currency\Provider\CurrencyProviderInterface`
  * `Sylius\Component\Currency\Context\ProviderBasedCurrencyContext`
  * `Sylius\Component\Currency\Provider\CurrencyProvider`

* `sylius_currency.currency` configuration option was removed as well as `sylius_currency.currency` parameter.

### Customer / CustomerBundle

### FixturesBundle

### Grid / GridBundle

* Custom options for filter form types was extracted from ``options`` to ``form_options`` in grid configuration.

  Before:
  ```yaml
      sylius_grid:
          grids:
              app_order:
                  filters:
                      channel:
                          type: entity
                          options:
                              class: "%app.model.channel%"
                              fields: [channel]
  ```

  After:
  ```yaml
      sylius_grid:
          grids:
              app_order:
                  filters:
                      channel:
                          type: entity
                          options:
                              fields: [channel]
                          form_options:
                              class: "%app.model.channel%"
  ```

* Grid configuration was upgraded to allow setting the number of maximum visible items per page on index.

  ```yaml
      sylius_grid:
          grids:
              app_order:
                  limits: [15, 20, 30]
  ```

### Inventory / InventoryBundle

### Locale / LocaleBundle

* `Locale` model's `$enabled` field has been removed along with all logic depending on it.

### Mailer / MailerBundle

### MoneyBundle

### Order / OrderBundle

* The `ExpiredCartsRemover` service has been moved from the component and into the bundle.
  In addition it dispatches the `sylius.carts.pre_remove` and `sylius.carts.post_remove` events, both of which hold
  the collection of carts to be, or already removed, depending on the event.
  Also, as of now, it depends on the `sylius.manager.order` to remove the carts instead of
  the repository in order to not flush every outdated `cart`, but the whole collection.

* Moved `Sylius\Component\Order\Factory\AddToCartCommandFactoryInterface` to `Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface`.

### Payment / PaymentBundle

* Changed default ``Payment::$state`` from *new* to *cart*.

* Credit Card model and all related code have been removed.

* `PaymentInterface::getSource()` and `PaymentInterface::setSource(PaymentSourceInterface $source)` were removed.

* `PaymentSourceInterface` has been removed.

* `void` transition and state has been removed due to being not used.

### PayumBundle

* There were changes made with handling payment states:

  * *authorized* is treated as *processing*
  * *payedout* is treated as *refunded*

* Removed `Payment::$gateway` property and corresponding methods.

* Introduced `PaypalGatewayConfigurationType` and `StripeGatewayConfigurationType` for dynamic gateways configuration.

### Product / ProductBundle

* `ProductVariant::$name` property (and corresponding getter and setter) was removed to make it translatable.
  Therefore, `ProductVariantTranslation` was introduced with one `$name` property.
  All product variants names are migrated to new concept with migration `Version2016121415313`.
  Look at [this PR](https://github.com/Sylius/Sylius/pull/7091) if you have any problems with upgrade.

* `ProductAssociationType::$name` property (and corresponding getter and setter) was removed to make it translatable.
  Therefore, `ProductAssociationTypeTranslation` was introduced with one `$name` property.
  All product association types names are migrated to new concept with migration `Version20161219160441`.
  Look at [this PR](https://github.com/Sylius/Sylius/pull/7134) if you have any problems with upgrade.

* `Product::$availableOn` and `Product::$availableUntil` properties (and corresponding getters and setters) were removed.
  Look at [this PR](https://github.com/Sylius/Sylius/pull/7451) if you have any problems with upgrade.

* `ProductVariant::$availableOn` and `ProductVariant::$availableUntil` properties (and corresponding getters and setters) were removed.

* `ProductInterface::getAvailableVariants()` method was removed as well.

* `ProductVariantRepositoryInterface::findOneByCode($code)` method has been replaced with `ProductVariantRepositoryInterface::findOneByCodeAndProductCode($code, $productCode)`.

* `ProductVariantRepositoryInterface::findOneByIdAndProductId($id, $productId)` method signature was added.

### Promotion / PromotionBundle

* Removed `Sylius\Bundle\PromotionBundle\Form\EventListener\AbstractConfigurationSubscriber`,
  `Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionActionFormSubscriber` and
  `Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionRuleFormSubscriber` event subscribers,
  use `Sylius\Bundle\PromotionBundle\Form\Type\ConfigurablePromotionElementType` as parent type instead.

### Registry / RegistryBundle

### Resource / ResourceBundle

* Removed `sylius_resource.resources.*.translation.fields` configuration key, it was not used at all - if causing issues,
  remove your configuration under it.

* Moved `Sylius\Bundle\ResourceBundle\Model\ResourceLogEntry` to `Sylius\Component\Resource\Model\ResourceLogEntry`.

### Review / ReviewBundle

* Service `sylius.average_rating_updater` name has been changed to `sylius.product_review.average_rating_updater` and
  service `sylius.listener.review_change` name has been changed to `sylius.listener.product_review_change`
  These services will be generated automatically based on subject name.

### Shipping / ShippingBundle

### ShopBundle

* The following templates were moved:

  * `@SyliusShop/Homepage/_header.html.twig` -> `@SyliusShop/_header.html.twig`
  * `@SyliusShop/Homepage/_footer.html.twig` -> `@SyliusShop/_footer.html.twig`
  * `@SyliusShop/Homepage/Menu/_security.html.twig` -> `@SyliusShop/Menu/_security.html.twig`
  * `@SyliusShop/_currencySwitch.html.twig` -> `@SyliusShop/Menu/_currencySwitch.html.twig`
  * `@SyliusShop/_localeSwitch.html.twig` -> `@SyliusShop/Menu/_localeSwitch.html.twig`

* `HomepageController` has been made final and can't be extended anymore, follow Symfony best practices and do not extend it.
  Instead extend the `Symfony\Bundle\FrameworkBundle\Controller\Controller` and override the `sylius.controller.shop.homepage` service definition.

### Taxation / TaxationBundle

* Signature of method `findChildren(string $parentCode)` in `Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface`
  was changed to `findChildren(string $parentCode, string $locale)`.

* Removed `Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonChoiceType` and `Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonCodeChoiceType` form types.
  Use `Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType` instead.

* Removed method `findNodesTreeSorted()` from `Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface` - provide your own implementation instead.

### Taxonomy / TaxonomyBundle

### ThemeBundle

* `Sylius\Bundle\ThemeBundle\Translation\Provider\Locale\FallbackLocalesProvider` and `Sylius\Bundle\ThemeBundle\Translation\Provider\Locale\FallbackLocalesProviderInterface` have been removed.

* The fallback locales generation of `Sylius\Bundle\ThemeBundle\Translation\Translator` has been nerfed to more strongly rely on symfony's default logic.
  From now on it won't compute every possible permutation of fallback locales from the given one, but only the themeless version,
  the base locale with and without theme's modifier, and every pre-configured fallback with and without the modifier.

### UiBundle

* `Sylius\Bundle\UiBundle\Menu\AbstractMenuBuilder` was removed, you should add the following code to classes previously extending it:

  ```php
  use Knp\Menu\FactoryInterface;
  use Symfony\Component\EventDispatcher\EventDispatcher;

  /**
   * @var FactoryInterface
   */
  private $factory;

  /**
   * @var EventDispatcher
   */
  private $eventDispatcher;

  /**
   * @param FactoryInterface $factory
   * @param EventDispatcher $eventDispatcher
   */
  public function __construct(FactoryInterface $factory, EventDispatcher $eventDispatcher)
  {
      $this->factory = $factory;
      $this->eventDispatcher = $eventDispatcher;
  }
  ```

  Also `sylius.menu_builder` service was removed, you should add the following code to services previously extending it:

  ```xml
  <argument type="service" id="knp_menu.factory" />
  <argument type="service" id="event_dispatcher" />
  ```

### User / UserBundle

## Application:

* `sylius_admin_dashboard_redirect` route was removed, use `sylius_admin_dashboard` instead.

* All shop routes became prepended with locale code, see below for required routing and security changes.

* Shop only shows products / taxons having translations in current language.

### Configuration

* Move `sylius_shop` routing below `sylius_admin` and `sylius_admin_api` in `app/config/routing.yml` and replace it with the following one:

  ```yaml
  sylius_shop:
      resource: "@SyliusShopBundle/Resources/config/routing.yml"
      prefix: /{_locale}
      requirements:
          _locale: ^[a-z]{2}(?:_[A-Z]{2})?$

  sylius_shop_default_locale:
      path: /
      methods: [GET]
      defaults:
          _controller: sylius.controller.shop.locale_switch:switchAction
  ```

* Payum gateways configuration is now done in Admin panel. Don't use `yml` file to configure your custom gateways.

* While providing multiple locales you need to insert the two letter base (i.e. `en`), along with the `%locale%` parameter, to the fallbacks array in `app/config/config.yml`.

  ```yaml
  framework:
      translator: { fallbacks: ["%locale%", "en"] }
  ```

* Payum routes were made independent of the current locale. Add the following code to `app/config/routing.yml`:

  ```yaml
  sylius_shop_payum:
      resource: "@SyliusShopBundle/Resources/config/routing/payum.yml"
  ```

* Add exception config to `fos_rest` in `config.yml`:

```yml
fos_rest:
    exception: ~
```

### Security

* Firewalls configuration was changed to provide better CSRF protection and turn on remember me feature, update your `app/config/security.yml`:

  ```yaml
  security:
      firewalls:
          admin:
              form_login:
                  csrf_token_generator: security.csrf.token_manager
                  csrf_parameter: _csrf_admin_security_token
                  csrf_token_id: admin_authenticate
              remember_me:
                  secret: "%secret%"
                  path: /admin
                  name: APP_ADMIN_REMEMBER_ME
                  lifetime: 31536000
                  remember_me_parameter: _remember_me
          shop:
              form_login:
                  csrf_token_generator: security.csrf.token_manager
                  csrf_parameter: _csrf_shop_security_token
                  csrf_token_id: shop_authenticate
              remember_me:
                  secret: "%secret%"
                  name: APP_SHOP_REMEMBER_ME
                  lifetime: 31536000
                  remember_me_parameter: _remember_me
  ```

  From now on you need to pass CSRF token to your login-check request so you need to add `<input type="hidden" value={{ csrf_token('csrf_token_id') }} name="csrf_parameter">` into your login form.
  Example input for admin login looks like `<input type="hidden" name="_csrf_admin_security_token" value="{{ csrf_token('admin_authenticate') }}">`.

  The remember me feature did not work properly due to missing additional configuration.

* Securing partial routes and prepending shop URLs with locales need changes in `access_control` section of your `app/config/security.yml`:

  ```yaml
    security:
        access_control:
            - { path: "^/[^/]++/_partial", role: IS_AUTHENTICATED_ANONYMOUSLY, ips: [127.0.0.1, ::1] }
            - { path: "^/[^/]++/_partial", role: ROLE_NO_ACCESS }

            - { path: "^/[^/]++/login", role: IS_AUTHENTICATED_ANONYMOUSLY }

            - { path: "^/(?!admin|api)[^/]++/register", role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: "^/(?!admin|api)[^/]++/verify", role: IS_AUTHENTICATED_ANONYMOUSLY }

            - { path: "^/admin", role: ROLE_ADMINISTRATION_ACCESS }
            - { path: "^/api", role: ROLE_API_ACCESS }
            - { path: "^/(?!admin|api)[^/]++/account", role: ROLE_USER }
  ```
### Database Migrations

* Check if the Sylius migrations are in your `app/migrations` directory. If not, then add to this directory
  the migrations from the `vendor/sylius/sylius/app/migrations/` directory.

  If you've got your own migrations here, please run the migrations carefully. The doctrine migrations system is comparing dates of the migrations,
  then if some of your migrations have the same dates as migrations in Sylius, then they may corrupt the sequence of running Sylius migrations.

  In such situations we suggest running migrations one by one, instead of all at once.

### Behat

* `Sylius\Behat\Page\Admin\Crud\IndexPage`, `Sylius\Behat\Page\Admin\Crud\CreatePage`, `Sylius\Behat\Page\Admin\Crud\UpdatePage` now accepts route name instead of resource name.

