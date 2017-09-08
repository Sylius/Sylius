Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your `composer.json` and download package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require sylius/inventory-bundle

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require sylius/inventory-bundle

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to the kernel.
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

Let's assume we want to implement a book store application and track the books inventory.

You have to create a `Book` and an `InventoryUnit` entity, living inside your application code.
We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``AppBundle`` registered under ``App\Bundle\AppBundle`` namespace.

We will create `Book` entity.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/Book.php
    namespace App\AppBundle\Entity;

    use Sylius\Component\Inventory\Model\StockableInterface;
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

        public function __construct()
        {
            $this->onHand = 1;
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

In order to track the books inventory our `Book` entity must implement `StockableInterface`.
Note that we added ``->getSku()`` method which is alias to ``->getIsbn()``, this is the power of the interface,
we now have full control over the entity mapping.
In the same way ``->getInventoryName()`` exposes the book title as the displayed name for our stockable entity.

The next step requires the creating of the `InventoryUnit` entity, let’s do this now.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/InventoryUnit.php
    namespace App\AppBundle\Entity;

    use Sylius\Component\Inventory\Model\InventoryUnit as BaseInventoryUnit;
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

Note that we are using base model from Sylius component, which means inheriting some functionality inventory component provides.
`InventoryUnit` holds the reference to stockable object, which is `Book` in our case.
So, if we use the `InventoryOperator` to create inventory units, they will reference the given book entity.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_inventory:
        driver: doctrine/orm
        resources:
            inventory_unit:
                classes:
                    model: App\AppBundle\Entity\InventoryUnit


Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php bin/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.
