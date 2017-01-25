# UPGRADE FROM 1.0.0-beta.1 to 1.0.0-beta.2

* Bundles, container extensions and bundles configurations were made final and can't be extended anymore, follow Symfony
  best practices and do not extend them.

## Packages:

### Addressing / AddressingBundle

### AdminBundle

* Route `sylius_admin_order_shipment_ship` has been added to have specific end point only for updating via http PUT method and `sylius_admin_partial_shipment_ship` route is only for rendering the form.
* Rename any `sylius_admin_address_log_entry_index` usages to `sylius_admin_partial_log_entry_index`.

### ApiBundle

* All api routes are prefixed with version number. For example route: `/api/customers` has changed to `/api/v1/customers`. 

### Attribute / AttributeBundle

* `AttributeValue::$localeCode` property was added to make it translatable. Now, every attribute value has a locale code to be displayed properly in different locales. All attribute values are migrated to the new concept with migration `Version20170109143010`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7219) if you have any problems with upgrade.

### Channel / ChannelBundle

### Core / CoreBundle

* `ImageUniqueCode` and `ImageUniqueCodeValidator` were deleted and replaced by `UniqueWithinCollectionConstraintValidator`, `UniqueWithinCollectionConstraint` from `ResourceBundle`.
  To use it replace name of constraint in constraint mapping file from: `Sylius\Bundle\CoreBundle\Validator\Constraints\ImageUniqueCode`
  to: `Sylius\Bundle\ResourceBundle\Validator\Constraints\UniqueWithinCollectionConstraint`

* Renamed ``getLastNewPayment()`` on ``OrderInterface`` to ``getLastPayment($state)``, where ``$state`` is target last payment state. Every ``getLastNewPayment()`` method should be replaced with ``getLastPayment(PaymentInterface::STATE_NEW)``.

* `Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor` and `Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver` 
  have become a zone scope aware. From now, only zones with scope 'shipping' or 'all' will be considered in `Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver` 
  and a scope 'tax' or 'all' is required in `Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor`. 
  A migration file has been prepared  which fill in "all" as scope for zones that didn't have it specified, so they will be resolved by new implementation.

* State resolvers have been made final. In order to change theirs behavior please decorate them or provide your own implementation.

### Currency / CurrencyBundle

### Customer / CustomerBundle

### FixturesBundle

### Grid / GridBundle

* Custom options for filter form types was extracted from ``options`` to ``form_options`` in grid configuration.

Before:
```php
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
```php
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

### Inventory / InventoryBundle

### Locale / LocaleBundle

### Mailer / MailerBundle

### MoneyBundle

### Order / OrderBundle

### Payment / PaymentBundle

* Changed default ``Payment::$state`` from *new* to *cart*.
* Credit Card model and all related code have been removed.
* `->getSource()` and `->setSource(PaymentSourceInterface $source)` have been removed from `PaymentInterface`.
* `PaymentSourceInterface` has been removed.

### PayumBundle

### Product / ProductBundle

* `ProductVariant::$name` property (and corresponding getter and setter) was removed to make it translatable. Therefore, `ProductVariantTranslation` was introduced with one `$name` property. All product variants names are migrated to new concept with migration `Version2016121415313`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7091) if you have any problems with upgrade.
* `ProductAssociationType::$name` property (and corresponding getter and setter) was removed to make it translatable. Therefore, `ProductAssociationTypeTranslation` was introduced with one `$name` property. All product association types names are migrated to new concept with migration `Version20161219160441`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7134) if you have any problems with upgrade.

### Promotion / PromotionBundle

### Registry / RegistryBundle

### Resource / ResourceBundle

### Review / ReviewBundle

Service `sylius.average_rating_updater` name has been changed to `sylius.product_review.average_rating_updater` and service `sylius.listener.review_change` name has been changed to `sylius.listener.product_review_change`
These services will be generated automatically based on subject name. 

### Shipping / ShippingBundle

### ShopBundle

### Taxation / TaxationBundle

### Taxonomy / TaxonomyBundle

### ThemeBundle

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

### Configuration

### Security

Some change has been made to `app/config/security.yml`
  ```yaml
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
                lifetime: 31536000 //custom lifetime
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
From now one you need to pass csrf token to your login-check request so you need to add `<input type="hidden" value={{ csrf_token('csrf_token_id') }} name="csrf_parameter">` into your login form.
Example input `<input type="hidden" name="_csrf_admin_security_token" value="{{ csrf_token('admin_authenticate') }}">`

The remember me feature did not work properly due to missing additional configuration.

### Behat

* `Sylius\Behat\Page\Admin\Crud\IndexPage`, `Sylius\Behat\Page\Admin\Crud\CreatePage`, `Sylius\Behat\Page\Admin\Crud\UpdatePage` now accepts route name instead of resource name.

