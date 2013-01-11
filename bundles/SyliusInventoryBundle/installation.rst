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
                model: App\AppBundle\Entity\Product

Routing configuration
-------------------------------

Import routing configuration by adding folowing to your `app/config/routing.yml``.

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
