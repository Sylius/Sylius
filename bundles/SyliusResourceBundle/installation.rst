Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your ``composer.json`` and download the package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require "sylius/resource-bundle":"1.0.*@dev"

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require "sylius/resource-bundle":"1.0.*@dev"

Adding required bundles to the kernel
-------------------------------------

You just need to enable proper bundles inside the kernel.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
        );
    }

To benefit from bundle services, you have to first register your bundle class as a *resource*.

You also need to enable HTTP method parameter override, by calling the following method on the **Request** object.

.. code-block:: php

    use Symfony\Component\HttpFoundation\Request;

    Request::enableHttpMethodParameterOverride();

Registering model as resource
-----------------------------

.. code-block:: yaml

    sylius_resource:
        resources:
            app.user:
                driver: doctrine/orm
                templates: App:User
                classes:
                    model: App\Entity\User

And... we're done!

This configuration registers for you several services and service aliases.

First of all, it gives you **app.manager.user**, which is simple alias to a proper **ObjectManager** service.
For *doctrine/orm* it will be your default entity manager, and unless you want to stay completely storage agnostic, you can use
the entity (or document) manager the "usual way".

Secondly, you get an **app.repository.user**. It represents repository. This service by default has a custom class, which implements
``Sylius\\Bundle\\ResourceBundle\\Model\\RepositoryInterface`` (which extends the Doctrine **ObjectRepository**).

The last and most important service is **app.controller.user**, you'll learn about it in next section.
