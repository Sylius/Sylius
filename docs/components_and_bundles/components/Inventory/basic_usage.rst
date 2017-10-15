Basic Usage
===========

Stockable Object
----------------

The first thing you should do it is implementing stockable object.
Example implementation:

.. code-block:: php

    <?php

    class Product implements StockableInterface
    {
        /**
         * Get stock keeping unit.
         *
         * @return mixed
         */
        public function getSku()
        {
            // TODO: Implement getSku() method.
        }

        /**
         * Get inventory displayed name.
         *
         * @return string
         */
        public function getInventoryName()
        {
            // TODO: Implement getInventoryName() method.
        }

        /**
         * Simply checks if there any stock available.
         *
         * @return Boolean
         */
        public function isInStock()
        {
            // TODO: Implement isInStock() method.
        }

        /**
         * Get stock on hold.
         *
         * @return integer
         */
        public function getOnHold()
        {
            // TODO: Implement getOnHold() method.
        }

        /**
         * Set stock on hold.
         *
         * @param integer
         */
        public function setOnHold($onHold)
        {
            // TODO: Implement setOnHold() method.
        }

        /**
         * Get stock on hand.
         *
         * @return integer
         */
        public function getOnHand()
        {
            // TODO: Implement getOnHand() method.
        }

        /**
         * Set stock on hand.
         *
         * @param integer $onHand
         */
        public function setOnHand($onHand)
        {
            // TODO: Implement setOnHand() method.
        }
    }

.. _component_inventory_operator_inventory-operator:

InventoryOperator
-----------------

The **InventoryOperator** provides basic operations on your inventory.

.. code-block:: php

    <?php

    use Sylius\Component\Inventory\Operator\InventoryOperator;
    use Sylius\Component\Inventory\Checker\AvailabilityChecker;
    use Sylius\Component\Resource\Repository\InMemoryRepository;

    $inMemoryRepository = new InMemoryRepository(); // Repository model.
    $product = new Product(); // Stockable model.
    $eventDispatcher; // It gives a possibility to hook before or after each operation.
    // If you are not familiar with events, check the symfony Event Dispatcher.

    $availabilityChecker = new AvailabilityChecker(false);
    $inventoryOperator = new InventoryOperator($availabilityChecker, $eventDispatcher);

    $product->getOnHand(); // Output will be 0.
    $inventoryOperator->increase($product, 5);
    $product->getOnHand(); // Output will be 5.

    $product->getOnHold(); // Output will be 0.
    $inventoryOperator->hold($product, 4);
    $product->getOnHold(); // Output will be 4.

    $inventoryOperator->release($product, 3);
    $product->getOnHold(); // Output will be 1.

Decrease
~~~~~~~~

.. code-block:: php

    <?php

    use Sylius\Component\Inventory\Operator\InventoryOperator;
    use Sylius\Component\Inventory\Checker\AvailabilityChecker;
    use Doctrine\Common\Collections\ArrayCollection;
    use Sylius\Component\Inventory\Model\InventoryUnit;
    use Sylius\Component\Inventory\Model\InventoryUnitInterface;

    $inventoryUnitRepository; // Repository model.
    $product = new Product(); // Stockable model.
    $eventDispatcher; // It gives possibility to hook before or after each operation.
    // If you are not familiar with events. Check symfony event dispatcher.

    $availabilityChecker = new AvailabilityChecker(false);
    $inventoryOperator = new InventoryOperator($availabilityChecker, $eventDispatcher);
    $inventoryUnit1 = new InventoryUnit();
    $inventoryUnit2 = new InventoryUnit();
    $inventoryUnits = new ArrayCollection();
    $product->getOnHand(); // Output will be 5.

    $inventoryUnit1->setStockable($product);
    $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD);

    $inventoryUnit2->setStockable($product);

    $inventoryUnits->add($inventoryUnit1);
    $inventoryUnits->add($inventoryUnit2);

    count($inventoryUnits); // Output will be 2.
    $inventoryOperator->decrease($inventoryUnits);
    $product->getOnHand(); // Output will be 4.

.. caution::
    All methods in **InventoryOperator** throw `InvalidArgumentException`_ or `InsufficientStockException`_ if an error occurs.

.. _InsufficientStockException: http://api.sylius.org/Sylius/Component/Inventory/Operator/InsufficientStockException.html

.. _InvalidArgumentException: http://php.net/manual/en/class.invalidargumentexception.php

.. note::
    For more detailed information go to `Sylius API InventoryOperator`_.

.. _Sylius API InventoryOperator: http://api.sylius.org/Sylius/Component/Inventory/Operator/InventoryOperator.html

.. hint::
    To understand how events work check `Symfony EventDispatcher`_.

.. _Symfony EventDispatcher: http://symfony.com/doc/current/components/event_dispatcher/introduction.html

.. _component_inventory_operator_noop-inventory-operator:

NoopInventoryOperator
---------------------

In some cases, you may want to have unlimited inventory, this operator will allow you to do that.

.. hint::
    This operator is based on the null object pattern. For more detailed information go to `Null Object pattern`_.

.. _Null Object pattern: https://en.wikipedia.org/wiki/Null_Object_pattern

.. note::
    For more detailed information go to `Sylius API NoopInventoryOperator`_.

.. _Sylius API NoopInventoryOperator: http://api.sylius.org/Sylius/Component/Inventory/Operator/NoopInventoryOperator.html

.. _component_inventory_checker_availability-checker:

AvailabilityChecker
-------------------

The **AvailabilityChecker** checks availability of a given stockable object.
To characterize an object which is an **AvailabilityChecker**, it needs to implement the :ref:`component_inventory_checker_availability-checker-interface`.
Second parameter of the ``->isStockSufficient()`` method gives a possibility to check for a given quantity of a stockable.

.. code-block:: php

    <?php

    use Sylius\Component\Inventory\Checker\AvailabilityChecker;

    $product = new Product(); // Stockable model.
    $product->getOnHand(); // Output will be 5
    $product->getOnHold(); // Output will be 4

    $availabilityChecker = new AvailabilityChecker(false);
    $availabilityChecker->isStockAvailable($product); // Output will be true.
    $availabilityChecker->isStockSufficient($product, 5); // Output will be false.

.. _component_inventory_factory_inventory-unit-factory:

InventoryUnitFactory
--------------------

The **InventoryUnitFactory** creates a collection of new inventory units.

.. code-block:: php

    <?php

    use Sylius\Component\Inventory\Factory\InventoryUnitFactory;
    use Sylius\Component\Inventory\Model\InventoryUnitInterface;

    $inventoryUnitRepository; // Repository model.
    $product = new Product(); // Stockable model.

    $inventoryUnitFactory = new InventoryUnitFactory($inventoryUnitRepository);

    $inventoryUnits = $inventoryUnitFactory->create($product, 10, InventoryUnitInterface::STATE_RETURNED);
    // Output will be collection of inventory units.

    $inventoryUnits[0]->getStockable(); // Output will be your's stockable model.
    $inventoryUnits[0]->getInventoryState(); // Output will be 'returned'.
    count($inventoryUnits); // Output will be 10.

.. note::
    For more detailed information go to `Sylius API InventoryUnitFactory`_.

.. _Sylius API InventoryUnitFactory: http://api.sylius.org/Sylius/Component/Inventory/Factory/InventoryUnitFactory.html
