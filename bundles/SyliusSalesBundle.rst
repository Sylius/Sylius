SyliusSalesBundle
======================

Highly flexible Sales engine.

Installation
------------

Installing dependencies
~~~~~~~~~~~~~~~~~~~~~~~

This bundle uses **Pagerfanta library** and **PagerfantaBundle**.

The installation guide can be found `here <https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle>`_.

Downloading the bundle
~~~~~~~~~~~~~~~~~~~~~~

The good practice is to download it to `vendor/bundles/Sylius/Bundle/SalesBundle`.

This can be done in several ways, depending on your preference.

The first method is the standard Symfony2 method.

Using the vendors script
************************

Add the following lines in your `deps` file. ::

    [SyliusSalesBundle]
        git=git://github.com/Sylius/SyliusSalesBundle.git
        target=bundles/Sylius/Bundle/SalesBundle

Now, run the vendors script to download the bundle.

.. code-block:: bash

    $ php bin/vendors install

Using submodules
****************

If you prefer instead to use git submodules, then run the following lines.

.. code-block:: bash

    $ git submodule add git://github.com/Sylius/SyliusSalesBundle.git vendor/bundles/Sylius/Bundle/SalesBundle
    $ git submodule update --init

Autoloader configuration
~~~~~~~~~~~~~~~~~~~~~~~~

Add the `Sylius\\Bundle` namespace to your autoloader.

.. code-block:: php

    <?php

    // app/autoload.php

    $loader->registerNamespaces(array(
        'Sylius\\Bundle' => __DIR__.'/../vendor/bundles'
    ));

Adding bundle to kernel
~~~~~~~~~~~~~~~~~~~~~~~

Finally, enable the bundle in the kernel...

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Sylius\Bundle\SalesBundle\SyliusSalesBundle(),
        );
    }

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    This part is not written yet.

Container configuration
~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    This part is not written yet.

Updating database schema
~~~~~~~~~~~~~~~~~~~~~~~~

The last thing you need to do is updating the database schema.

For "**ORM**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

Usage guide
-----------

.. note::

    This part is not written yet.

Configuration reference
-----------------------

.. note::

    This part is not written yet.
                
Testing and continous integration
----------------------------------

.. image:: http://travis-ci.org/Sylius/SyliusSalesBundle.png

This bundle uses `travis-ci.org <http://travis-ci.org/Sylius/SyliusSalesBundle>`_ for CI.

Before running tests, load the dependencies using `Composer <http://packagist.org>`_.

    .. code-block:: bash

        $ wget http://getcomposer.org/composer.phar
        $ php composer.phar install

Now you can test by simply using this command.

    .. code-block:: bash

        $ phpunit

Working examples
----------------

If you want to see this and other bundles in action, try out the `Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_.

It's open sourced github project.

Dependencies
------------

This bundle uses the awesome `Pagerfanta library <https://github.com/whiteoctober/Pagerfanta>`_ and `Pagerfanta bundle <https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusSalesBundle/issues>`_.
If you have found bug, please create an issue.
