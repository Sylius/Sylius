.. rst-class:: outdated

Installation
============

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

We assume you're familiar with `Composer <https://packagist.org>`_, a dependency manager for PHP.
Use following command to add the bundle to your `composer.json` and download package.

If you have `Composer installed globally <https://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    composer require sylius/addressing-bundle

Otherwise you have to download .phar file.

.. code-block:: bash

    curl -sS https://getcomposer.org/installer | php
    php composer.phar require sylius/addressing-bundle:*

Adding required bundles to the kernel
-------------------------------------

You need to enable the bundle inside the kernel.

If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new winzou\Bundle\StateMachineBundle\winzouStateMachineBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new BabDev\PagerfantaBundle\BabDevPagerfantaBundle(),

            new Sylius\Bundle\AddressingBundle\SyliusAddressingBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );
    }


Updating database schema
------------------------

Run the following command.

.. code-block:: bash

    php bin/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.
