Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your ``composer.json`` and download the package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require "sylius/resource-bundle"

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require "sylius/resource-bundle"

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

    <?php

    // web/app{_dev|_test}.php

    use Symfony\Component\HttpFoundation\Request;

    Request::enableHttpMethodParameterOverride();
