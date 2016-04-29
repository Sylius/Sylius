Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your `composer.json` and download the package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require sylius/settings-bundle

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require sylius/settings-bundle

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to the kernel.
This bundle also uses `DoctrineCacheBundle`. Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Sylius\Bundle\SettingsBundle\SyliusSettingsBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );
    }

.. note::

    Please register the bundle before *DoctrineBundle*. This is important as we use listeners which have to be processed first.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_settings:
        driver: doctrine/orm

    doctrine_cache:
        providers:
            sylius_settings:
                type: file_system

Importing routing configuration
-------------------------------

Import default routing from your ``app/config/routing.yml``.

.. code-block:: yaml

    sylius_settings:
        resource: "@SyliusSettingsBundle/Resources/config/routing.yml"
        prefix: /settings

.. note::

    We used ``default`` namespace in this example. If you want to use other namespaces for saving your settings, routing config should
    be updated as it contains the namespace parameter.

Updating database schema
------------------------

Run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.
