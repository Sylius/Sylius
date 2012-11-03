SyliusCartBundle .. image:: http://travis-ci.org/Sylius/SyliusCartBundle.png
================

A generic solution for building carts inside Symfony2 applications, it does not matter if you are
starting new project or you need to implement this feature for existing system - this bundle should be helpful.
Currently only the Doctrine ORM driver is implemented, so we'll use it here as example.
There are two main models inside the bundle, `Cart` and `CartItem`.
The second one will be the most interesting for us, as the cart is created as a quite sensible default.
Currently the bundle requires a bit of coding from you, but we're working on simplifying the adaption process.

Installation
------------

We assume you're familiar with `Composer <http://packagist.org>`_.
Add this to yours `composer.json`.

.. code-block:: json

    "require": {
        "sylius/cart-bundle": "*"
    },

And install it by typing following command.

.. code-block:: bash

    $ php composer.phar update sylius/cart-bundle

Adding required bundles to kernel
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now you need to enable the bundle inside kernel.
If you're not using any other Sylius bundles, you also need to add `SyliusResourceBundle` to kernel.
Do not worry, it was automatically installed for you by Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\CartBundle\SyliusCartBundle(),
        );
    }

Container configuration
~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    This part is not written yet.

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    This part is not written yet.

Updating database schema
~~~~~~~~~~~~~~~~~~~~~~~~

The last thing you need to do is to update the database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

This should be done only in dev environment, we recommend using Doctrine migrations, to safely update your schema.

Usage guide
-----------

.. note::

    This part is not written yet.

Configuration reference
-----------------------

.. note::

    This part is not written yet.

`phpspec2 <http://phpspec.net>`_ Specifications
-----------------------------------------------

.. code-block:: bash

    $ wget http://getcomposer.org/composer.phar
    $ php composer.phar install --dev
    $ php bin/phpspec run

Working examples
----------------

If you want to see working implementation, try out the `Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusCartBundle/issues>`_.
If you have found bug, please create an issue.
