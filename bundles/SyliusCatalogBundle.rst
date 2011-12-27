SyliusCatalogBundle
===================

Categorizing whatever you want just got easier.

You can use this bundle to create multiple categorized catalogs of any object.

Installation
------------

Installing dependencies
~~~~~~~~~~~~~~~~~~~~~~~

This bundle uses **Pagerfanta library** and **PagerfantaBundle**.

The installation guide can be found `here <https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle>`_.

Downloading the bundle
~~~~~~~~~~~~~~~~~~~~~~

The good practice is to download it to `vendor/bundles/Sylius/Bundle/CatalogBundle`.

This can be done in several ways, depending on your preference.

The first method is the standard Symfony2 method.

Using the vendors script
************************

Add the following lines in your `deps` file. ::

    [SyliusCatalogBundle]
        git=git://github.com/Sylius/SyliusCatalogBundle.git
        target=bundles/Sylius/Bundle/CatalogBundle

Now, run the vendors script to download the bundle.

.. code-block:: bash

    $ php bin/vendors install

Using submodules
****************

If you prefer instead to use git submodules, then run the following lines.

.. code-block:: bash

    $ git submodule add git://github.com/Sylius/SyliusCatalogBundle.git vendor/bundles/Sylius/Bundle/CatalogBundle
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
            new Sylius\Bundle\CatalogBundle\SyliusCatalogBundle(),
        );
    }

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now is the time to import routing files. Open up your `routing.yml` file. 

Customize the prefixes or whatever you want.

.. code-block:: yaml

    sylius_catalog_category:
        resource: @SyliusCatalogBundle/Resources/config/routing/frontend/category.yml

    sylius_catalog_backend_category:
        resource: @SyliusCatalogBundle/Resources/config/routing/backend/category.yml
        prefix: /administration

.. note::
    
    The bundle requires at least one catalog created.

Usage guide
-----------

`Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_ is a great example of this bundle usage.

There are two confiured catalogs, one simple categories set for blog posts and one nested set of product categories.

Catalogs configuration
~~~~~~~~~~~~~~~~~~~~~~

By **catalog** we understand a categorized set of objects.

This is confiuguration used in sandbox app.

.. code-block:: yaml

    sylius_catalog:
        driver: ORM
        catalogs:
            blog:
                property: "posts"
                classes:
                    model: Sylius\Sandbox\Bundle\BloggerBundle\Entity\Category
                templates:
                    backend:
                        list: SandboxBloggerBundle:Backend/Category:list.html.twig
                        show: SandboxBloggerBundle:Backend/Category:show.html.twig
                        create: SandboxBloggerBundle:Backend/Category:create.html.twig
                        update: SandboxBloggerBundle:Backend/Category:update.html.twig
                    frontend:
                        list: SandboxBloggerBundle:Frontend/Category:list.html.twig
                        show: SandboxBloggerBundle:Frontend/Category:show.html.twig
            assortment:
                property: "products"
                nested: true
                sorter: sylius_assortment.sorter.product
                classes:
                    model: Sylius\Sandbox\Bundle\AssortmentBundle\Entity\Category
                    form: Sylius\Sandbox\Bundle\AssortmentBundle\Form\Type\CategoryFormType
                templates:
                    backend:
                        list: SandboxAssortmentBundle:Backend/Category:list.html.twig
                        show: SandboxAssortmentBundle:Backend/Category:show.html.twig
                        create: SandboxAssortmentBundle:Backend/Category:create.html.twig
                        update: SandboxAssortmentBundle:Backend/Category:update.html.twig
                    frontend:
                        list: SandboxAssortmentBundle:Frontend/Category:list.html.twig
                        show: SandboxAssortmentBundle:Frontend/Category:show.html.twig

Testing and continous integration
----------------------------------

.. image:: http://travis-ci.org/Sylius/SyliusCatalogBundle.png

This bundle uses `travis-ci.org <http://travis-ci.org/Sylius/SyliusCatalogBundle>`_ for CI.

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

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusCatalogBundle/issues>`_.
If you have found bug, please create an issue.
