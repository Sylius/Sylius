SyliusCategorizerBundle
=======================

Categorizing whatever you want just got easier. Grouping products, posts or any other model is common feature in most of modern web applications.
So why implement it every time you need it? You can use this bundle to create multiple categorized catalogs of any object.
It provides all controllers, routing, base mapping and services that will boost you development.

Features
--------

* Base support for different many different persistence layers. Currently only Doctrine ORM driver is implemented.
* Allows you to create custom ordered flat list of categories, default controllers and forms will handle CRUD and moving up/down the categories.
* Thanks to `Doctrine Extensions library <http://github.com/l3pp4rd/DoctrineExtensions>`_ you can have nested set of categories, just extend proper class, modify form, add little mapping and it works.
* Handles both many-to-one and many-to-many relations between objects and the categories. Bundle will check it for you.
* You can create as many catalogs as you want, by `catalog` we understand set of categories and the items, for example products or blog posts.
* It uses `Pagerfanta <https://github.com/whiteoctober/Pagerfanta>`_ to paginate over the category items, but you can easily disable the pagination for specific catalog.
* Thanks to awesome `Symfony2 <http://symfony.com>`_ everything is configurable and extensible.
* Unit tested.

Installation
------------

Installing dependencies
~~~~~~~~~~~~~~~~~~~~~~~

Recommended way of managing dependencies for Sylius bundles is using `Composer <http://getcomposer.org>`_.

This bundle uses **Pagerfanta library** and **PagerfantaBundle**.

The installation guide can be found `here <https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle>`_.

Installation via Composer
~~~~~~~~~~~~~~~~~~~~~~~~~

Create a `composer.json` file in your project root and add this.

.. code-block:: json

    {
        "require": {
            "sylius/categorizer-bundle": "dev-master"
        }
    }

Then, download composer and install deps.

.. code-block:: bash

    $ wget http://getcomposer.org/composer.phar
    $ php composer.phar install

This should download all required libraries with the bundle itself.
You can use the Composer autoloader or define paths manually in your own `autoload.php`.

Downloading the bundle manually
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The good practice is to download it to `vendor/bundles/Sylius/Bundle/CategorizerBundle`.

This can be done in several ways, depending on your preference.

The first method is the standard Symfony2 method.

Using the vendors script
************************

Add the following lines in your `deps` file. ::

    [SyliusCategorizerBundle]
        git=git://github.com/Sylius/SyliusCategorizerBundle.git
        target=bundles/Sylius/Bundle/CategorizerBundle

Now, run the vendors script to download the bundle.

.. code-block:: bash

    $ php bin/vendors install

Using submodules
****************

If you prefer instead to use git submodules, then run the following lines.

.. code-block:: bash

    $ git submodule add git://github.com/Sylius/SyliusCategorizerBundle.git vendor/bundles/Sylius/Bundle/CategorizerBundle
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
            new Sylius\Bundle\CategorizerBundle\SyliusCategorizerBundle(),
        );
    }

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now is the time to import routing files. Open up your `routing.yml` file. 

Customize the prefixes or whatever you want.

.. code-block:: yaml

    sylius_categorizer_category:
        resource: @SyliusCategorizerBundle/Resources/config/routing/frontend/category.yml

    sylius_categorizer_backend_category:
        resource: @SyliusCategorizerBundle/Resources/config/routing/backend/category.yml
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

    sylius_categorizer:
        driver: doctrine/orm
        catalogs:
            assortment: # Catalog alias.
                property: "products" # Property used in your category model object to store items.
                model: Sylius\Sandbox\Bundle\AssortmentBundle\Entity\Category # Your category class.
                form: sylius_sandbox_assortment_category # Your category form type used when creating/updating category. Default form is just one text field, "name".
                pagination:
                    mpp: 6 # Max per page.
                templates:
                    backend:
                        list: SandboxAssortmentBundle:Backend/Category:list.html.twig
                        show: SandboxAssortmentBundle:Backend/Category:show.html.twig
                        create: SandboxAssortmentBundle:Backend/Category:create.html.twig
                        update: SandboxAssortmentBundle:Backend/Category:update.html.twig
                    frontend:
                        list: SandboxAssortmentBundle:Frontend/Category:list.html.twig
                        show: SandboxAssortmentBundle:Frontend/Category:show.html.twig
            blog:
                property: "posts"
                model: Sylius\Sandbox\Bundle\BloggerBundle\Entity\Category
                pagination:
                    disable: true # Disable pagination, just view all items. They will be retrived by using ->getPosts() category model method.
                templates:
                    backend:
                        list: SandboxBloggerBundle:Backend/Category:list.html.twig
                        show: SandboxBloggerBundle:Backend/Category:show.html.twig
                        create: SandboxBloggerBundle:Backend/Category:create.html.twig
                        update: SandboxBloggerBundle:Backend/Category:update.html.twig
                    frontend:
                        list: SandboxBloggerBundle:Frontend/Category:list.html.twig
                        show: SandboxBloggerBundle:Frontend/Category:show.html.twig

Testing and continous integration
----------------------------------

.. image:: http://travis-ci.org/Sylius/SyliusCategorizerBundle.png

This bundle uses `travis-ci.org <http://travis-ci.org/Sylius/SyliusCategorizerBundle>`_ for CI.

Before running tests, load the dependencies using `Composer <http://getcomposer.org>`_.

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

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusCategorizerBundle/issues>`_.
If you have found bug, please create an issue.
