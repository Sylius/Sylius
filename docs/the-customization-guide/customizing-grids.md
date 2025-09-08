# Customizing Grids

Grids in Sylius are responsible for rendering lists of entities in the administration panel, such as products, customers, or orders.\
You can customize grids declaratively in YAML or programmatically using event listeners.

{% hint style="info" %}
&#x20;If you’re new to Sylius grids, we recommend starting with the [SyliusGridBundle documentation](https://stack.sylius.com/grid/index).
{% endhint %}

### Why Customize a Grid?

Typical use cases include:

* Removing or modifying fields
* Changing or disabling filters
* Adjusting grid actions (e.g., buttons)
* Reordering items
* Programmatically extending grids with PHP

### YAML-Based Customizations

The recommended and most common way to customize grids in Sylius 2.0 is by editing the `config/packages/_sylius.yaml` file.

#### ✅ Removing a Field

```yaml
# config/packages/_sylius.yaml
sylius_grid:
    grids:
        sylius_admin_product_review:
            fields:
                title:
                    enabled: false
```

This will hide the `title` field on the product review grid.

#### ✅ Modifying a Field

```yaml
# config/packages/_sylius.yaml
sylius_grid:
    grids:
        sylius_admin_product_review:
            fields:
                date:
                    label: "When was it added?"
```

You can also localize the label using the translation system — see the Customizing Translations guide.

***

#### ✅ Removing a Filter

```yaml
# config/packages/_sylius.yaml
sylius_grid:
    grids:
        sylius_admin_product_review:
            filters:
                title:
                    enabled: false
```

***

#### ✅ Removing an Action

```yaml
# config/packages/_sylius.yaml
sylius_grid:
    grids:
        sylius_admin_product_review:
            actions:
                item:
                    delete:
                        type: delete
                        enabled: false
```

This disables the `delete` action on each row.

***

#### ✅ Modifying an Action

You can customize an action's label and destination route:

```yaml
# config/packages/_sylius.yaml
sylius_grid:
    grids:
        sylius_admin_product:
            actions:
                item:
                    show:
                        type: show
                        label: Show in the shop
                        options:
                            link:
                                route: sylius_shop_product_show
                                parameters:
                                    slug: resource.slug
```

{% hint style="warning" %}
The `show` action doesn't exist on the product grid by default - make sure it is added first.
{% endhint %}

***

#### ✅ Reordering Fields, Filters, or Actions

Use the `position` option to control order:

```yaml
# config/packages/_sylius.yaml
sylius_grid:
    grids:
        sylius_admin_product_review:
            fields:
                status:
                    position: 1
                reviewSubject:
                    position: 2
                rating:
                    position: 3
                author:
                    position: 4
                date:
                    position: 5
                title:
                    position: 6
```

This reorders the fields top-to-bottom in the grid.

***

### PHP-Based Customizations (via Events)

In Sylius 2.0, grids can also be extended programmatically. Each grid dispatches an event during configuration.

#### Example: Modifying the Product Grid in PHP

Let’s say we want to:

* Remove the `image` field
* Add a `variantSelectionMethod` field

```php
<?php

namespace App\Grid;

use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Sylius\Component\Grid\Definition\Field;

final class AdminProductsGridListener
{
    public function editFields(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();

        $grid->removeField('image');

        $variantSelection = Field::fromNameAndType('variantSelectionMethod', 'string');
        $variantSelection->setLabel('Variant Selection');

        $grid->addField($variantSelection);
    }
}
```

{% hint style="warning" %}
Be careful not to re-add a field that already exists. Doing so will throw a `LogicException`.
{% endhint %}

#### Registering the Listener

```yaml
# config/services.yaml
services:
    App\Grid\AdminProductsGridListener:
        tags:
            - { name: kernel.event_listener, event: sylius.grid.admin_product, method: editFields }
```

***

### Advanced configuration: Pagination Options

The following options are enabled by default on all grids:

```yaml
pagination:
    fetch_join_collection: true
    use_output_walkers: true
```

These options improve pagination performance on large datasets (especially for databases with over 1M rows) but may increase the number of executed queries.\
If your use case requires disabling them, you can override them per grid:

```yaml
# config/packages/_sylius.yaml
sylius_grid:
    grids:
        sylius_admin_product_review:
            driver:
                name: doctrine/orm
                options:
                    pagination:
                        fetch_join_collection: false
                        use_output_walkers: false
```

{% hint style="info" %}
This is an optional performance optimization toggle - not required unless you encounter pagination or performance issues.
{% endhint %}
