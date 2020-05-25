.. rst-class:: plus-doc

How to create a custom inventory sources filter?
================================================

In this guide, we will create a new inventory source filter with the lowest priority, that provides
inventory source with the lowest stock.

1. Implement LeastItemsInventorySourcesFilter
---------------------------------------------

First of all, the inventory sources filter has to implement the
``Sylius\Plus\Inventory\Application\Filter\InventorySourcesFilterInterface``.

Then implement the behaviour inside of the ``filter`` method of your service:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Inventory\Filter;

    use Sylius\Plus\Entity\ProductVariantInterface;
    use Sylius\Plus\Inventory\Application\Filter\InventorySourcesFilterInterface;
    use Sylius\Plus\Inventory\Domain\Model\InventorySourceInterface;
    use Sylius\Plus\Inventory\Domain\Model\VariantsQuantityMapInterface;

    final class LeastItemsInventorySourcesFilter implements InventorySourcesFilterInterface
    {
        public function filter(array $inventorySources, VariantsQuantityMapInterface $variantsQuantityMap): array
        {
            $inventorySourcesItems = [];

            /** @var InventorySourceInterface $inventorySource */
            foreach ($inventorySources as $inventorySource) {
                $inventorySourcesItems[$inventorySource->getCode()] = 0;

                foreach ($variantsQuantityMap->iterate() as $variantData) {
                    /** @var ProductVariantInterface $variant */
                    $variant = $variantData['variant'];

                    if (!$variant->isTracked()) {
                        continue;
                    }

                    $stock = $variant->getInventorySourceStockForInventorySource($inventorySource);

                    $inventorySourcesItems[$inventorySource->getCode()] += $stock->getAvailable();
                }
            }

            return [$this->getInventorySourceByCode(
                $inventorySources,
                array_search(min($inventorySourcesItems), $inventorySourcesItems)
            )];
        }

        private function getInventorySourceByCode(array $inventorySources, string $code): ?InventorySourceInterface
        {
            /** @var InventorySourceInterface $inventorySource */
            foreach ($inventorySources as $inventorySource) {
                if ($inventorySource->getCode() === $code) {
                    return $inventorySource;
                }
            }

            return null;
        }
    }

2. Register the custom filter with defined priority
---------------------------------------------------

Your filtering service has to be registered in the ``config/services.yaml`` file
with ``sylius_plus.inventory.inventory_sources_filter`` tag and ``priority`` attribute set, as we can see below:

.. code-block:: yaml

    services:
        App\Inventory\Filter\LeastItemsInventorySourcesFilter:
            tags:
                - { name: 'sylius_plus.inventory.inventory_sources_filter', priority: -10 }


After registering the filter, with such a priority, it will be invoked as the last one, after the default filters
provided in Sylius Plus.

That's all you have to do to customize the inventory sources resolving strategy in your application.

Learn more
----------

* :doc:`Multi-Source Inventory concept documentation </book/products/multi_source_inventory>`
* :doc:`Single Source Inventory concept documentation </book/products/inventory>`
* :doc:`Order concept documentation </book/orders/orders>`

.. image:: ../../_images/sylius_plus/banner.png
    :align: center
    :target: https://sylius.com/plus/?utm_source=docs
