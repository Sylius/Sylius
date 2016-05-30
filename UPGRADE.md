UPGRADE
=======

## From 0.18 to 0.19.x

### Core and CoreBundle

* Introduced new adjustments type ``ORDER_UNIT_PROMOTION``
* Changed current *item* promotion actions to *unit* promotion actions (as they're applied on ``OrderItemUnit`` level)
* Introduced ``getDiscountedUnitPrice`` method on ``OrderItem``, which returns single *unit* price lowered by ``ORDER_UNIT_PROMOTION`` adjustments

### Variation and VariationBundle

* Removed concept of master variant (removed ``$master`` flag from ``Sylius\Component\Variation\Model\Variant``), all usages of **master** variant has been, for now, replaced with **first** variant;

## From 0.17 to 0.18.x

### Application

* Moved some of the parameters out of parameters.yml.dist file, please check your configurations;
* Moved parameters are now in ``CoreBundle/Resource/config/app.parameters.yml``, you should import them before your own parameters.yml file;
* Renamed basic parameters to match Symfony Standard's conventions:

Before:

```yaml
%sylius.database.host%
%sylius.locale%

# etc.
```

After:

```yaml
%database_host%
%locale%
```

### HWIOAuthBundle is now optional 

HWIOAuthBundle for social logins is no longer a required dependency. If you would like to use it in your project, you should add it to composer.json's ``require`` section, install it and add proper configuration for routing:

```yml
# routing.yml

hwi_oauth_security:
    resource: "@HWIOAuthBundle/Resources/config/routing/login.xml"
    prefix: /connect-login
 
hwi_oauth_redirect:
    resource: "@HWIOAuthBundle/Resources/config/routing/redirect.xml"
    prefix: /connect
 
amazon_login:
    path: /connect-login/check-amazon
 
facebook_login:
    path: /connect-login/check-facebook
 
google_login:
    path: /connect-login/check-google
```

And for security:

```yml
# security.yml

# For your shop firewall, configure "oauth" section:

oauth:
    resource_owners:
        amazon:   "/connect-login/check-amazon"
        facebook: "/connect-login/check-facebook"
        google:   "/connect-login/check-google"
        login_path:   /login
        failure_path: /login
        oauth_user_provider:
            service: sylius.oauth.user_provider
```

### Translation and TranslationBundle

* Merged ``Translation`` component with ``Resource`` component
* Merged ``TranslationBundle`` with ``ResourceBundle``
* Renamed ``TranslatableResourceRepository`` to ``TranslatableRepository``

### Core and CoreBundle

* Removed "exclude" option from ``taxon`` rule
* Changed ``ORDER_PROMOTION_ADJUSTMENT``s to be added on ``OrderItemUnit`` level instead of ``Order`` level, based on distributed promotion amount 

### SettingsBundle

* Renamed `sylius_settings_all()` Twig function to `sylius_settings()`
* Removed `sylius_settings_get('foo.property')` and `sylius_settings_has('foo.property')` Twig functions, use 
  `sylius_settings('foo').property` and `sylius_settings('foo').property is defined` instead

## From 0.16 to 0.17.x

### Promotion and PromotionBundle

* Changed "item_count" promotion type into "cart_quantity". It now checks cart quantity instead different items number.

### Resource and SyliusResourceBundle

 * All resources must implement ``Sylius\Component\Resource\Model\ResourceInterface``;
 * ResourceController has been rewritten from scratch but should maintain 100% of previous functionality;
 * ``$this->config`` is no longer available and you should create it manually in every action;

Before:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        return $this->render($this->config->getTemplate('custom.html'));
    }
}
```

After:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        return $this->render($configuration->getTemplate('custom.html'));
    }
}
```

 * Custom view handler has been introduced and ResourceController no longer extends FOSRestController:

Before:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        return $this->handleView($this->view(null, 204));
    }
}
```

After:

```php
<?php

namespace AppBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        return $this->viewHandler->handle($configuration, View::create(null, 204));
    }
}
```

 * DomainManager has been replaced with standard manager and also repository is injected into the controller;

Before:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        // ...

        $this->domainManager->create($book);
        $this->domainManager->update($book);
        $this->domainManager->delete($book);
    }
}
```

After:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        // ...

        $this->repository->add($book);
        $this->manager->flush();
        $this->repository->remove($book);
    }
}
```

 * ``getForm()`` has been removed in favor of properly injected service;

Before:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        // ...

        $form = $this->getForm($book);
    }
}
```

After:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        // ...

        $form = $this->resourceFormFactory->create($configuration, $book);
    }
}
```

 * Events are no longer dispatched by the removed "DomainManager".

Before:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        $this->domainManager->create($book);
    }
}
```

After:

```php
<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

class BookController extends ResourceController
{
    public function customAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $book);
        $this->repository->add($book);
        $event = $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $book);
    }
}
```

### Core and CoreBundle

* Moved ``taxCategory`` from ``Product`` to ``ProductVariant``;
* ``OrderItem`` model from Core component no longer implements ``PromotionSubjectInterface``. Remove all usages of ``addPromotion``, etc. from your custom code. Custom actions targeting items need to be adjusted - see ``ContainsProductRuleChecker`` for reference.

### Addressing and SyliusAddressingBundle

* Extracted ``Country`` ISO code to name translation, from model to a twig extension: ``CountryNameExtension``;
* Removed ``Address`` relations to ``Country`` and ``Province`` objects, their unique ``code`` is used instead;
* Removed specific ``ZoneMembers`` i.e. ``ProvinceZoneMember`` in favor of a dynamic ``ZoneMember``;
* https://github.com/Sylius/Sylius/pull/3696
* ``exchangeRate`` is now recorded for ``Order`` at time of purchase for accurate cross-currency reporting.

### Order and SyliusOrderBundle

* Introduced ``OrderItemUnit``, which represents every single unit in ``Order``;
* Replaced ``InventoryUnit`` with ``OrderItemUnit`` in the core. This entity will be used as ``InventoryUnit`` and ``ShipmentItem``;
* Removed ``setQuantity()`` method from ``OrderItem``;
* Introduced ``OrderItemUnitFactory`` creating unit for specific ``OrderItem`` by ``createForItem()`` method;
* Introduced ``OrderItemQuantityModifier`` that is used to control ``OrderItem`` quantity and units;
* Introduced ``OrderItemQuantityDataMapper``, which attached to ``OrderItemType`` uses proper service to modify ``OrderItem`` quantity;
* Changed ``Adjustment`` ``description`` field to ``label``;

### Shipping and ShippingBundle

* Renamed ``ShipmentItem`` to ``ShipmentUnit`` to align with full-stack ``OrderItemUnit`` that it represents and avoid confusion
against the similarly named ``OrderItem``.
* Also renamed all associated 'item' wording to 'unit' in forms and form configuration (e.g. ``DefaultCalculators::PER_UNIT_RATE`` and ``RuleInterface::TYPE_UNIT_TOTAL``).
* Shipping resources config must be updated:

Before:

```yml
 sylius_shipping:
     resources:
         shipment_item:
              classes:
                  model: %sylius.model.order_item_unit.class%
```

After:

```yml
 sylius_shipping:
     resources:
         shipment_unit:
              classes:
                  model: %sylius.model.order_item_unit.class%
```

### Currency

``CurrencyConverterInterface`` ``convert()`` method renamed to ``convertFromBase()``.

### Content
```bash
#!/bin/sh

set -ex

app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\ContentBundle\Doctrine\Phpcr\StaticContent" "Sylius\Bundle\ContentBundle\Document\StaticContent"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route" "Sylius\Bundle\ContentBundle\Document\Route"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\RedirectRoute" "Sylius\Bundle\ContentBundle\Document\RedirectRoute"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu" "Sylius\Bundle\ContentBundle\Document\Menu"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode" "Sylius\Bundle\ContentBundle\Document\MenuNode"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SlideshowBlock" "Sylius\Bundle\ContentBundle\Document\SlideshowBlock"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ImagineBlock" "Sylius\Bundle\ContentBundle\Document\ImagineBlock"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock" "Sylius\Bundle\ContentBundle\Document\ActionBlock"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\MenuBlock" "Sylius\Bundle\ContentBundle\Document\MenuBlock"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock" "Sylius\Bundle\ContentBundle\Document\ReferenceBlock"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock" "Sylius\Bundle\ContentBundle\Document\SimpleBlock"
app/console doctrine:phpcr:document:migrate-class "Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock" "Sylius\Bundle\ContentBundle\Document\StringBlock"
```

## From 0.15.0 to 0.16.x

### General

 * Configuration structure for all bundles has changed:

Before:

```yml
sylius_taxation:
    validation_groups:
        tax_category: [sylius, custom]
    classes:
        tax_category:
            model: AppBundle\Entity\TaxCategory
            form: AppBundle\Form\Type\TaxCategoryType
```

After:

```yml
sylius_taxation:
    resources:
        tax_category:
            classes:
                model: AppBundle\Entity\TaxCategory
                form:
                    default: AppBundle\Form\Type\TaxCategoryType
            validation_groups:
                default: [sylius, custom]
```

 * Validation groups parameters have been renamed:

Before:

```
%sylius.validation_group.product%
```

After:

```
%sylius.validation_group**s**.product%
```

### Attribute and SyliusAttributeBundle

 * Attribute system has been reworked and now every ``type`` is represented by ``AttributeTypeInterface`` instance;
 * https://github.com/Sylius/Sylius/pull/3608.

### SyliusPayumBundle

 * Changed configuration key `sylius_payum.classes.payment_config` to `sylius_payum.classes.gateway_config`;
 * ``PaymentConfig`` renamed to ``GatewayConfig``;

### Resource & SyliusResourceBundle

 * ``RepositoryInterface`` now has two additional methods `add` and `remove`;
 * Added ``InMemoryRepository`` which stores resources in memory;
 * Added ``DriverInterface`` which replaced previously used abstractions;
 * Reworked ``AbstractResourceExtension`` to be much simpler.

## From 0.14.0 to 0.15.x

### General

 * We no longer use FOSUserBundle;
 * User provider has been changed https://github.com/Sylius/Sylius/pull/2717/files#diff-da1af97fca8a5fcb6fb7053584105ba7R6;
 * Everything related to e-commerce (orders, addresses, groups and coupons) are now associated with Customer;
 * Everything related to system account remains on User entity;
 * Email no longer exist on Order;
 * All order are associated with Customer (even guest orders - during guest checkout Customer is created based on email);
 * User must have associated Customer;
 * Email no longer exist on User. It is on Customer now;
 * In the checkout we depend on Customer not User;
 * In templates in many places we use Customer instead of User entity now.

### Channel & SyliusChannelBundle

https://github.com/Sylius/Sylius/pull/2752

### User & SyliusUserBundle

https://github.com/Sylius/Sylius/pull/2717

### Database

 * Call ``` sylius:rbac:initialize ``` to create new roles in your system;
 * Execute migration script to migrate your data into the new model schema.

**The migration script migrates only default data, if you have some customizations on any of affected entities you should take care of them yourself!**

### API Client

https://github.com/Sylius/Sylius/pull/2887

### SyliusApiBundle

When you create server client in Sylius, it's public id was a combination of Client internal id and it's random id. For example:

```
client_id: 1_mpO5ZJ35hx
```

now it is simply random id, so it will be changed to:

```
client_id: mpO5ZJ35hx
```

**Remember to update your API clients using Sylius!**

Related discussion https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/issues/328.

### Addressing

* Removed `CountryTranslation`, using `Intl` Symfony component instead to provide translated country names based on ISO country code. https://github.com/Sylius/Sylius/pull/3035

## From 0.9.0 to 0.10.x

Version 0.10.x includes the new Sylius e-commerce components.
All classes without Symfony dependency have been moved to separate ``Sylius\Component`` namespace.

VariableProductBundle has been merged into ProductBundle.
Its functionality extracted into two separate bundles - SyliusAttributeBundle & SyliusVariationBundle.

Property model has been renamed to Attribute.

Before performing this procedure, please create a safe backup of your database.
This upgrade changes significantly the way product attributes and options are stored in the database.
We do provide a way to migrate your data, but no rollback will be possible in case of a problem.

In addition to the components split, we have switched to state-machine in order to deal with states, instead of
hard-coded states. You can now configure all the states you want and the transitions between them. Please refer to
`state-machine.yml` that you can find in the bundles using it. *Most events have been replaced by state-machine events,
much more powerful. Please update your listeners to make them callbacks of state-machine transitions. Again, please
refer to the state-machine configuration files to do so.*

The signature of `PaymentInterface::setDetails` method was changed. Now it allows either array or instance of \Traversable.

### Addressing

Model classes and ZoneMatcher services have been moved to ``Sylius\Component\Addressing`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\AddressingBundle\\Model/Sylius\\Component\\Addressing\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\AddressingBundle\\ZoneMatcher/Sylius\\Component\\Addressing\\ZoneMatcher/g' {} \;
```

### Cart

Model classes and several services have been moved to ``Sylius\Component\Cart`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CartBundle\\Model/Sylius\\Component\\Cart\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CartBundle\\Provider\\CartProviderInterface/Sylius\\Component\\Cart\\Provider\\CartProviderInterface/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CartBundle\\Storage\\CartStorageInterface/Sylius\\Component\\Cart\\Storage\\CartStorageInterface/g' {} \;
```

Twig extension class name & service were changed:

* `SyliusCartExtension` into `CartExtension`,
* `sylius.cart_twig` into `sylius.twig.extension.cart`

### Core

All Symfony independent code has been moved to ``Sylius\Component\Core`` namespace.
Variant model has been renamed to ProductVariant and VariantImage to ProductVariantImage.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Model\\Variant/Sylius\\Component\\Core\\Model\\ProductVariant/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Model/Sylius\\Component\\Core\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Calculator/Sylius\\Component\\Core\\Calculator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\OrderProcessing/Sylius\\Component\\Core\\OrderProcessing/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Promotion/Sylius\\Component\\Core\\Promotion/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Uploader/Sylius\\Component\\Core\\Uploader/g' {} \;
```

Twig extension class name & service were changed:

* `SyliusMoneyExtension` into `SyliusMoneyExtension`,
* `SyliusRestrictedZoneExtension` into `RestrictedZoneExtension`,
* `sylius.twig.restricted_zone_extension` into `sylius.twig.extension.restricted_zone`

### Inventory

Model classes and all Symfony-agnostic services have been moved to ``Sylius\Component\Inventory`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Model/Sylius\\Component\\Inventory\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Checker/Sylius\\Component\\Inventory\\Checker/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Factory/Sylius\\Component\\Inventory\\Factory/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Operator/Sylius\\Component\\Inventory\\Operator/g' {} \;
```

Twig extension class name & service were changed:

* `SyliusInventoryExtension` into `InventoryExtension`,
* `sylius.inventory_twig` into `sylius.twig.extension.inventory`

### Money

Model classes and interfaces have been moved to ``Sylius\Component\Money`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\MoneyBundle\\Model/Sylius\\Component\\Money\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\MoneyBundle\\Converter/Sylius\\Component\\Money\\Converter/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\MoneyBundle\\Context\\CurrencyContextInterface/Sylius\\Component\\Money\\Context\\CurrencyContextInterface/g' {} \;
```

Twig extension class name & service were changed:

* `SyliusMoneyExtension` into `MoneyExtension`,
* `sylius.twig.money` into `sylius.twig.extension.money`

### Order

Model classes and repository interfaces have been moved to ``Sylius\Component\Order`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\OrderBundle\\Model/Sylius\\Component\\Order\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\OrderBundle\\Generator/Sylius\\Component\\Order\\Generator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\OrderBundle\\Repository/Sylius\\Component\\Order\\Repository/g' {} \;
```

### Payment

PaymentsBundle has been renamed to PaymentBundle.
Model classes interfaces have been moved to ``Sylius\Component\Order`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PaymentsBundle\\Model/Sylius\\Component\\Payment\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/PaymentsBundle/PaymentBundle/g' {} \;
```

Configuration root node has been adjusted as well.

Before:

```yaml
sylius_payments:
    driver: doctrine/orm
```

After:

```yaml
sylius_payment:
    driver: doctrine/orm
```

### Product

Previously, ProductBundle provided basic product model with properties support.
VariableProductBundle, with its options and variants support, has been merged into the basic bundle.
From now on, Sylius product catalog ships with variations support by default.

The concept of properties has been renamed to attributes.

* Property model becomes Attribute.
* ProductProperty becomes AttributeValue.

Attributes can be attached to any object and can be configured under ``sylius_attribute`` node.
The product bundle configures its attributes automatically.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\VariableProductBundle\\Model/Sylius\\Component\\Product\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.option/sylius.repository.product_option/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.option_value/sylius.repository.product_option_value/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.product_property/sylius.repository.product_attribute_value/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.property/sylius.repository.product_attribute/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.prototype/sylius.repository.product_prototype/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.variant/sylius.repository.product_variant/g' {} \;
```

Beware, the Doctrine relationship name has changed as well between `Variant` (now, `ProductVariant`) and `Product`. If you use it in custom repository methods, you need to adapt accordingly:

Before:

```yaml
variant.product
```

After:

```yaml
product_variant.object
```

### Promotion

PromotionsBundle has been renamed to PromotionBundle.
Model classes interfaces have been moved to ``Sylius\Component\Promotion`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/PromotionsBundle/PromotionBundle/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Model/Sylius\\Component\\Promotion\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Action/Sylius\\Component\\Promotion\\Action/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Checker/Sylius\\Component\\Promotion\\Checker/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Generator/Sylius\\Component\\Promotion\\Generator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Processor/Sylius\\Component\\Promotion\\Processor/g' {} \;
```

Configuration root node has been adjusted as well.

Before:

```yaml
sylius_promotions:
    driver: doctrine/orm
```

After:

```yaml
sylius_promotion:
    driver: doctrine/orm
```

### Resource

ResourceBundle model interfaces have been moved to ``Sylius\Component\Resource`` namespace.
``RepositoryInterface`` has been moved to ``Repository`` namespace under the component.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ResourceBundle\\Model/Sylius\\Component\\Resource\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Component\\Resource\\Model\\RepositoryInterface/Sylius\\Component\\Resource\\Repository\\RepositoryInterface/g' {} \;
```

Twig extension class name & service were changed:

* `SyliusResourceExtension` into `SyliusResourceExtension`,
* `sylius.twig.resource` into `sylius.twig.extension.resource`

### Settings

Twig extension class name & service were changed:

* `SyliusSettingsExtension` into `SettingsExtension`,
* `sylius.settings.twig` into `sylius.twig.extension.settings`

### Shipping

Model classes and Symfony agnostic services & interfaces have been moved to ``Sylius\Component\Shipping`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Model/Sylius\\Component\\Shipping\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Calculator/Sylius\\Component\\Shipping\\Calculator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Checker/Sylius\\Component\\Shipping\\Checker/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Resolver/Sylius\\Component\\Shipping\\Resolver/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Processor/Sylius\\Component\\Shipping\\Processor/g' {} \;
```

### Taxation

Model classes and Symfony agnostic services have been moved to ``Sylius\Component\Taxation`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxationBundle\\Model/Sylius\\Component\\Taxation\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxationBundle\\Calculator/Sylius\\Component\\Taxation\\Calculator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxationBundle\\Resolver/Sylius\\Component\\Taxation\\Resolver/g' {} \;
```

### Taxonomy

TaxonomiesBundle has been renamed to TaxonomyBundle.
Model classes interfaces have been moved to ``Sylius\Component\Taxonomy`` namespace.

```bash
$ find ./src -type f -exec sed -i 's/TaxonomiesBundle/TaxonomyBundle/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxonomyBundle\\Model/Sylius\\Component\\Taxonomy\\Model/g' {} \;
```

Configuration root node has been adjusted as well.

Before:

```yaml
sylius_taxonomies:
    driver: doctrine/orm
```

After:

```yaml
sylius_taxonomy:
    driver: doctrine/orm
```

### Web

Twig extension service name was changed:

* `sylius.twig.text_extension` into `sylius.twig.extension.text`
