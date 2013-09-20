Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use the following command to add the bundle to your `composer.json` and download the package.

.. code-block:: bash

    $ composer require sylius/sales-bundle:*

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
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new Sylius\Bundle\SalesBundle\SyliusSalesBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );
    }

.. note::

    Please register the bundle before *DoctrineBundle*. This is important as we use listeners which have to be processed first.

Creating your entities
----------------------

You have to create your own **Order** entity, living inside your application code.
We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``AppBundle`` registered under ``App\Bundle\AppBundle`` namespace.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/Order.php
    namespace App\AppBundle\Entity;

    use Sylius\Bundle\SalesBundle\Entity\Order as BaseOrder;

    class Order extends BaseOrder
    {
    }

Now we need to define simple mapping for this entity, because it only extends the Doctrine mapped superclass.
You should create a mapping file in your ``AppBundle``, put it inside the doctrine mapping directory ``src/App/AppBundle/Resources/config/doctrine/Order.orm.xml``.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                             xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                                 http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="App\AppBundle\Entity\Order" table="app_order">
            <id name="id" column="id" type="integer">
                <generator strategy="AUTO" />
            </id>
            <one-to-many field="items" target-entity="Sylius\Bundle\SalesBundle\Model\OrderItemInterface" mapped-by="order" orphan-removal="true">
                <cascade>
                    <cascade-all/>
                </cascade>
            </one-to-many>
            <one-to-many field="adjustments" target-entity="Sylius\Bundle\SalesBundle\Model\AdjustmentInterface" mapped-by="order" orphan-removal="true">
                <cascade>
                    <cascade-all/>
                </cascade>
            </one-to-many>
        </entity>

    </doctrine-mapping>

.. note::

    You might wonder why are we putting interface inside mapping, you can read about this Doctrine feature `here <http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html>`_.

Now let's assume you have a *Product* entity, which represents your main merchandise in your webshop.

.. note::

    Please remember that you can use anything else, *Product* here is just an obvious example, but it will work in similar way with other entities.

All you need to do is making your *Product* entity to implement ``SellableInterface`` and configure it inside Symfony settings.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/Product.php
    namespace App\AppBundle\Entity;

    use Sylius\Bundle\SalesBundle\Model\SellableInterface;

    class Product implements SellableInterface
    {
        // Your code...

        public function getSellableName()
        {
            // Here you just have to return the nice display name of your merchandise.
            return $this->name;
        }
    }

Now, you do not even have to map your *Product* model to the order items. It is all done automatically.
And that would be all about entities.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_sales:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.
        classes:
            sellable:
                model: App\AppBundle\Entity\Product # Your product entity.
            order:
                model: App\AppBundle\Entity\Order # The order entity.

Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.
