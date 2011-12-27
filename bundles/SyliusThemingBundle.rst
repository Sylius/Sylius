SyliusThemingBundle
======================

Highly flexible Theming engine.

Installation
------------

Downloading the bundle
~~~~~~~~~~~~~~~~~~~~~~

The good practice is to download it to `vendor/bundles/Sylius/Bundle/ThemingBundle`.

This can be done in several ways, depending on your preference.

The first method is the standard Symfony2 method.

Using the vendors script
************************

Add the following lines in your `deps` file. ::

    [SyliusThemingBundle]
        git=git://github.com/Sylius/SyliusThemingBundle.git
        target=bundles/Sylius/Bundle/ThemingBundle

Now, run the vendors script to download the bundle.

.. code-block:: bash

    $ php bin/vendors install

Using submodules
****************

If you prefer instead to use git submodules, then run the following lines.

.. code-block:: bash

    $ git submodule add git://github.com/Sylius/SyliusThemingBundle.git vendor/bundles/Sylius/Bundle/ThemingBundle
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
            new Sylius\Bundle\ThemingBundle\SyliusThemingBundle(),
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

.. image:: http://travis-ci.org/Sylius/SyliusThemingBundle.png

This bundle uses `travis-ci.org <http://travis-ci.org/Sylius/SyliusThemingBundle>`_ for CI.

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

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusThemingBundle/issues>`_.
If you have found bug, please create an issue.
