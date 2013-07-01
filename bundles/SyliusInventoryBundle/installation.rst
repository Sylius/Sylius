Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use following command to add the bundle to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/inventory-bundle:*

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new FOS\RestBundle\FOSRestBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\InventoryBundle\SyliusInventoryBundle(),
        );
    }

Creating your entities
----------------------

Let's assume we want to implement book store application and track books inventory.

You have to create `Book` and `InventoryUnit` entity, living inside your application code.
We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``AppBundle`` registered under ``App\Bundle\AppBundle`` namespace.

We will create `Book` entity.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/Book.php
    namespace App\AppBundle\Entity;

    use Sylius\Bundle\InventoryBundle\Model\StockableInterface;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="app_book")
     */
    class Book implements StockableInterface
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\Column(type="string")
         */
        protected $isbn;

        /**
         * @ORM\Column(type="string")
         */
        protected $title;

        /**
         * @ORM\Column(type="integer")
         */
        protected $onHand;

        /**
         * @ORM\Column(type="boolean")
         */
        protected $availableOnDemand;

        public function __construct()
        {
            $this->onHand = 1;
            $this->availableOnDemand = true;
        }

        public function getId()
        {
            return $this->id;
        }

        public function getIsbn()
        {
            return $this->isbn;
        }

        public function setIsbn($isbn)
        {
            $this->isbn = $isbn;
        }

        public function getSku()
        {
            return $this->getIsbn();
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getInventoryName()
        {
            return $this->getTitle();
        }

        public function isInStock()
        {
            return 0 < $this->onHand;
        }

        public function isAvailableOnDemand()
        {
            return $this->availableOnDemand;
        }

        public function setAvailableOnDemand($availableOnDemand)
        {
            $this->availableOnDemand = (Boolean) $availableOnDemand;
        }

        public function getOnHand()
        {
            return $this->onHand;
        }

        public function setOnHand($onHand)
        {
            $this->onHand = $onHand;
        }
    }

.. note::

    This example shows the full power of `StockableInterface`.
    Bundle also provides `Stockable` entity which implements `StockableInterface` for you.
    By extending `Stockable` entity, example above can be dramatically simplified.

In order to track books inventory our `Book` entity must implement `StockableInterface`.
Note that we added ``->getSku()`` method which is alias to ``->getIsbn()``, this is the power of the interface,
we have a full control over entity mapping.
Similar goes for ``->getInventoryName()`` which exposes book title as display name for our stockable entity.

Next step requires creating the `InventoryUnit` entity, let’s do this now.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/InventoryUnit.php
    namespace App\AppBundle\Entity;

    use Sylius\Bundle\InventoryBundle\Entity\InventoryUnit as BaseInventoryUnit;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="app_inventory_unit")
     */
    class InventoryUnit extends BaseInventoryUnit
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
    }

Note that we are using base entity from Sylius bundle, which means inheriting some functionality inventory bundle provides.
`InventoryUnit` holds the reference to stockable object, which is `Book` in our case.
So, if we use `InventoryOperator` to create inventory units, they will reference given book entity.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_inventory:
        driver: doctrine/orm
        backorders: true
        classes:
            unit:
                model: App\AppBundle\Entity\InventoryUnit
            stockable:
                model: App\AppBundle\Entity\Book

Routing configuration
-------------------------------

Import routing configuration by adding following to your `app/config/routing.yml``.

.. code-block:: yaml

    sylius_inventory:
        resource: @SyliusInventoryBundle/Resources/config/routing.yml


Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.

Templates
---------

Bundle provides default `bootstrap <http://twitter.github.com/bootstrap/>`_ templates.

.. note::

    You can check `our Sandbox app <https://github.com/Sylius/Sylius-Sandbox>`_ to see how to integrate it in your application.
