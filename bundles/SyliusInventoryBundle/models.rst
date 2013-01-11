Models
======

Here is a quick reference for the default models.

InventoryUnit
-------------

Each unit holds reference to stockable object and state, which can be **sold** or **backordered**.
It also provides some handy shortcut methods like `isSold`, `isBackordered` and `getSku`.

StockableInterface
------------------

In order to be able to track stock levels in your application, you must implement `StockableInterface`.
It uses SKU to identify stockable, need to provide display name and to check if stockable is available on demand.
It can get/set current stock level with `getOnHand` and `setOnHand` methods.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/Product.php
    namespace App/AppBundle/Entity;

    use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

    class Product implements StockableInterface
    {
        protected $onHand;
        protected $sku;
        protected $name;
        protected $availableOnDemand;

        public function __construct()
        {
            $this->onHand = 1;
        }

        public function getSku()
        {
            return $this->sku;
        }

        public function isInStock()
        {
            return 0 < $this->onHand;
        }

        public function getOnHand()
        {
            return $this->onHand;
        }

        public function setOnHand($onHand)
        {
            $this->onHand = $onHand;
        }

        public function getInventoryName()
        {
            return $this->name;
        }

        public function isAvailableOnDemand()
        {
            return $this->availableOnDemand;
        }

        public function setAvailableOnDemand($availableOnDemand)
        {
            $this->availableOnDemand = (Boolean) $availableOnDemand;
        }
    }
