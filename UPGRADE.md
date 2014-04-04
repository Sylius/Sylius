UPGRADE
=======

## From 0.9.0 to 0.10.x

Version 0.10.x includes the new Sylius e-commerce components. 
All classes without Symfony dependency have been moved to separate ``Sylius\Component`` namespace.

VariableProductBundle has been merged into ProductBundle.
Its functionality extracted into two separate bundles - SyliusAttributeBundle & SyliusVariationBundle.

Property model has been renamed to Attribute.

Before performing this procedure, please create a safe backup of your database.
This upgrade changes significantly the way product attributes and options are stored in the database.
We do provide a way to migrate your data, but no rollback will be possible in case of a problem.

### Addressing

Model classes and ZoneMatcher services have been moved to ``Sylius\Component\Addressing`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\AddressingBundle\\Model/Sylius\\Component\\Addressing\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\AddressingBundle\\ZoneMatcher/Sylius\\Component\\Addressing\\ZoneMatcher/g' {} \;
```

### Cart

Model classes and several services have been moved to ``Sylius\Component\Cart`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CartBundle\\Model/Sylius\\Component\\Cart\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CartBundle\\Provider\\CartProviderInterface/Sylius\\Component\\Cart\\Provider\\CartProviderInterface/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CartBundle\\Storage\\CartStorageInterface/Sylius\\Component\\Cart\\Storage\\CartStorageInterface/g' {} \;
```

### Core

All Symfony independent code has been moved to ``Sylius\Component\Core`` namespace.
Variant model has been renamed to ProductVariant and VariantImage to ProductVariantImage.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Model\\Variant/Sylius\\Component\\Core\\Model\\ProductVariant/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Model/Sylius\\Component\\Core\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Calculator/Sylius\\Component\\Core\\Calculator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\OrderProcessing/Sylius\\Component\\Core\\OrderProcessing/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Promotion/Sylius\\Component\\Core\\Promotion/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\CoreBundle\\Uploader/Sylius\\Component\\Core\\Uploader/g' {} \;
```

### Inventory

### Inventory

Model classes and all Symfony-agnostic services have been moved to ``Sylius\Component\Inventory`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Model/Sylius\\Component\\Inventory\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Checker/Sylius\\Component\\Inventory\\Checker/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Factory/Sylius\\Component\\Inventory\\Factory/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\InventoryBundle\\Operator/Sylius\\Component\\Inventory\\Operator/g' {} \;
```

### Money

Model classes and interfaces have been moved to ``Sylius\Component\Money`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\MoneyBundle\\Model/Sylius\\Component\\Money\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\MoneyBundle\\Converter/Sylius\\Component\\Money\\Converter/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\MoneyBundle\\Context\\CurrencyContextInterface/Sylius\\Component\\Money\\Context\\CurrencyContextInterface/g' {} \;
```

### Order

Model classes and repository interfaces have been moved to ``Sylius\Component\Order`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\OrderBundle\\Model/Sylius\\Component\\Order\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\OrderBundle\\Generator/Sylius\\Component\\Order\\Generator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\OrderBundle\\Repository/Sylius\\Component\\Order\\Repository/g' {} \;
```

### Payment

PaymentsBundle has been renamed to PaymentBundle.
Model classes interfaces have been moved to ``Sylius\Component\Order`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PaymentsBundle\\Model/Sylius\\Component\\Payment\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/PaymentsBundle/PaymentBundle/g' {} \;
```

Configuration root node has been adjusted as well.

Before:

```
sylius_payments:
    driver: doctrine/orm
```

After:

```
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

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\VariableProductBundle\\Model/Sylius\\Component\\Product\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.option/sylius.repository.product_option/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.option_value/sylius.repository.product_option_value/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.product_property/sylius.repository.product_attribute_value/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.property/sylius.repository.product_attribute/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.prototype/sylius.repository.product_prototype/g' {} \;
$ find ./src -type f -exec sed -i 's/sylius.repository.variant/sylius.repository.product_variant/g' {} \;
```

### Promotion

PromotionsBundle has been renamed to PromotionBundle.
Model classes interfaces have been moved to ``Sylius\Component\Promotion`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/PromotionsBundle/PromotionBundle/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Model/Sylius\\Component\\Promotion\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Action/Sylius\\Component\\Promotion\\Action/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Checker/Sylius\\Component\\Promotion\\Checker/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Generator/Sylius\\Component\\Promotion\\Generator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\PromotionBundle\\Processor/Sylius\\Component\\Promotion\\Processor/g' {} \;
```

Configuration root node has been adjusted as well.

Before:

```
sylius_promotions:
    driver: doctrine/orm
```

After:

```
sylius_promotion:
    driver: doctrine/orm
```

### Resource

ResourceBundle model interfaces have been moved to ``Sylius\Component\Resource`` namespace.
``RepositoryInterface`` has been moved to ``Repository`` namespace under the component.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ResourceBundle\\Model/Sylius\\Component\\Resource\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Component\\Resource\\Model\\RepositoryInterface/Sylius\\Component\\Resource\\Repository\\RepositoryInterface/g' {} \;
```

### Shipping

Model classes and Symfony agnostic services & interfaces have been moved to ``Sylius\Component\Shipping`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Model/Sylius\\Component\\Shipping\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Calculator/Sylius\\Component\\Shipping\\Calculator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Checker/Sylius\\Component\\Shipping\\Checker/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Resolver/Sylius\\Component\\Shipping\\Resolver/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\ShippingBundle\\Processor/Sylius\\Component\\Shipping\\Processor/g' {} \;
```

### Taxation

Model classes and Symfony agnostic services have been moved to ``Sylius\Component\Taxation`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxationBundle\\Model/Sylius\\Component\\Taxation\\Model/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxationBundle\\Calculator/Sylius\\Component\\Taxation\\Calculator/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxationBundle\\Resolver/Sylius\\Component\\Taxation\\Resolver/g' {} \;
```

### Taxonomy

TaxonomiesBundle has been renamed to TaxonomyBundle.
Model classes interfaces have been moved to ``Sylius\Component\Taxonomy`` namespace.

``` bash
$ find ./src -type f -exec sed -i 's/TaxonomiesBundle/TaxonomyBundle/g' {} \;
$ find ./src -type f -exec sed -i 's/Sylius\\Bundle\\TaxonomyBundle\\Model/Sylius\\Component\\Taxonomy\\Model/g' {} \;
```

Configuration root node has been adjusted as well.

Before:

```
sylius_taxonomies:
    driver: doctrine/orm
```

After:

```
sylius_taxonomy:
    driver: doctrine/orm
```
