Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your ``composer.json`` and download the package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require sylius/fixtures-bundle

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require sylius/fixtures-bundle

Adding required bundles to the kernel
-------------------------------------

You need to enable the bundle inside the kernel, usually at the end of bundle list.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // Other bundles...
            new Sylius\Bundle\FixturesBundle\SyliusFixturesBundle(),
        );
    }
