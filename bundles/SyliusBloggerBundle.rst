SyliusBloggerBundle
======================

Highly flexible Blogger engine.

Installation
------------

Installing dependencies
~~~~~~~~~~~~~~~~~~~~~~~

This bundle uses **Pagerfanta library** and **PagerfantaBundle**.

The installation guide can be found `here <https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle>`_.

Downloading the bundle
~~~~~~~~~~~~~~~~~~~~~~

The good practice is to download it to `vendor/bundles/Sylius/Bundle/BloggerBundle`.

This can be done in several ways, depending on your preference.

The first method is the standard Symfony2 method.

Using the Composer
************************

Add the following lines in your `composer.json` file. ::

    "require": {
        ...
        "sylius/blogger-bundle": "dev-master"
    }

Now, run the vendors script to download the bundle.

.. code-block:: bash

    $ php composer.phar update

Using submodules
****************

If you prefer instead to use git submodules, then run the following lines.

.. code-block:: bash

    $ git submodule add git://github.com/Sylius/SyliusBloggerBundle.git vendor/bundles/Sylius/Bundle/BloggerBundle
    $ git submodule update --init

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
            new Sylius\Bundle\BloggerBundle\SyliusBloggerBundle(),
        );
    }

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    sylius_blogger_category:
        resource: @SyliusBloggerBundle/Resources/config/routing/frontend/post.yml
        prefix: /administration/posts

    sylius_blogger_backend:
        resource: @SyliusBloggerBundle/Resources/config/routing/backend/post.yml
        prefix: /administration/blog/posts

Or in XML format: 

.. code-block:: xml
    
    <import resource="@SyliusBloggerBundle/Resources/config/routing/frontend/post.yml" prefix="/blog/posts"/>
    <import resource="@SyliusBloggerBundle/Resources/config/routing/backend/post.yml" prefix="/administration/blog/posts" />

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

Create PostType class:

.. code-block:: php

    <?php
    namespace MyApp\Sylius\BloggerBundle\Form\Type;

    use Sylius\Bundle\BloggerBundle\Form\Type\PostType as BasePostType;
    use Symfony\Component\Form\FormBuilder;

    class PostType extends BasePostType
    {
        public function buildForm(FormBuilder $builder, array $options) 
        {
            parent::buildForm($builder, $options);
            // ...
        }
    }

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_blogger:
        driver: doctrine/orm
        engine: twig # or php
        classes:
            model:
                post: MyApp\Sylius\BloggerBundle\Entity\Post
            form:
                type:
                    post: MyApp\Sylius\BloggerBundle\Form\Type\PostType 
                
Testing and continous integration
----------------------------------

.. image:: http://travis-ci.org/Sylius/SyliusBloggerBundle.png

This bundle uses `travis-ci.org <http://travis-ci.org/Sylius/SyliusBloggerBundle>`_ for CI.

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

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusBloggerBundle/issues>`_.
If you have found bug, please create an issue.
