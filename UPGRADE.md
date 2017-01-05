# UPGRADE FROM 1.0.0-beta.1 to 1.0.0-beta.2

## Packages:

### Addressing / AddressingBundle

### AdminBundle

* New button **Create and add another** was added to default create buttons template. It's also possible to disable this feature, passing proper parameter to `vars` in routing generation.

```yml
app_custom_resource:
    resource: |
        alias: app.custom_resource
        ...
        vars:
            create:
                add_another: false
        ...
```

### ApiBundle

### Attribute / AttributeBundle

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

### PayumBundle

### Product / ProductBundle

* `ProductVariant::$name` property (and corresponding getter and setter) was removed to make it translatable. Therefore, `ProductVariantTranslation` was introduced with one `$name` property. All product variants names are migrated to new concept with migration `Version2016121415313`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7091) if you have any problems with upgrade.
* `ProductAssociationType::$name` property (and corresponding getter and setter) was removed to make it translatable. Therefore, `ProductAssociationTypeTranslation` was introduced with one `$name` property. All product association types names are migrated to new concept with migration `Version20161219160441`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7134) if you have any problems with upgrade.

### Promotion / PromotionBundle

### Registry / RegistryBundle

### Resource / ResourceBundle

* In routing generation, `redirect` parameter was changed from string to array with `create` and `update` keys, which specifies redirect routes for (separately) `create` and `update` action. Look at [this PR](https://github.com/Sylius/Sylius/pull/7152) if you have any problems with upgrade.

Before:
```yml
app_custom_resource:
    resource: |
        alias: app.custom_resource
        ...
        redirect: index
        ...
```

After:
```yml
app_custom_resource:
    resource: |
        alias: app.custom_resource
        ...
        redirect:
            create: index
            update: index
        ...
```

* In routing generation, it's possible to define variable in `redirect` configuration, to be able to pass it as parameter: `{{ path('sylius_custom_resource_create', {'redirect': 'sylius_custom_resource_update'}) }}`. Look at [this PR](https://github.com/Sylius/Sylius/pull/7152) if you have any problems with upgrade.

```yml
app_custom_resource:
    resource: |
        alias: app.custom_resource
        ...
        redirect:
            create: $redirect
            update: index
        ...
```

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

