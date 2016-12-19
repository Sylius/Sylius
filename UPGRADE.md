# UPGRADE FROM 1.0.0-beta.1 to 1.0.0-beta.2

## Packages:

### Addressing / AddressingBundle

* `Sylius\Component\Addressing\Matcher\ZoneMatcher` has been renamed to `Sylius\Component\Addressing\Resolver\ZoneResolver` made final, so you can no longer extend this class. 
Also, `Sylius\Component\Addressing\Matcher\AddressZoneMatcher` service has been introduced.
 If one has been extending a `Sylius\Component\Addressing\Resolver\ZoneResolver` class please use appropriate pattern or provide your custom implementation. 
 `Sylius\Component\Addressing\Matcher\AddressZoneMatcher` should make it easier to provide custom implementation with less overhead. 
 If the class has been overridden because of zones priority order, then no overriding would be required and priority can be defined in a yaml file as follows:
 ```yml
    sylius_addressing:
        zone_matcher_priorities: ['province', 'country', 'zone']
 ```

### AdminBundle

### ApiBundle

### Attribute / AttributeBundle

### Channel / ChannelBundle

### Core / CoreBundle

* `ImageUniqueCode` and `ImageUniqueCodeValidator` were deleted and replaced by `UniqueWithinCollectionConstraintValidator`, `UniqueWithinCollectionConstraint` from `ResourceBundle`.
  To use it replace name of constraint in constraint mapping file from: `Sylius\Bundle\CoreBundle\Validator\Constraints\ImageUniqueCode`
  to: `Sylius\Bundle\ResourceBundle\Validator\Constraints\UniqueWithinCollectionConstraint`

* Renamed ``getLastNewPayment()`` on ``OrderInterface`` to ``getLastPayment($state)``, where ``$state`` is target last payment state. Every ``getLastNewPayment()`` method should be replaced with ``getLastPayment(PaymentInterface::STATE_NEW)``.

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

### PayumBundle

### Product / ProductBundle

* `ProductVariant::$name` property (and corresponding getter and setter) was removed to make it translatable. Therefore, `ProductVariantTranslation` was introduced with one `$name` property. All product variants names are migrated to new concept with migration `Version2016121415313`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7091) if you have any problems with upgrade.

### Promotion / PromotionBundle

### Registry / RegistryBundle

### Resource / ResourceBundle

### Review / ReviewBundle

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

### Behat

* `Sylius\Behat\Page\Admin\Crud\IndexPage`, `Sylius\Behat\Page\Admin\Crud\CreatePage`, `Sylius\Behat\Page\Admin\Crud\UpdatePage` now accepts route name instead of resource name.

