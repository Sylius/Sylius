.. index::
   single: Bundles

SyliusAssortmentBundle
======================

Assortment bundle provides basic interfaces and mechanisms for product model.

Sorting, filters, CRUD, forms, events and more.

Flexible configuration and easy integration with other bundles.

Installation
------------

Installing dependencies
~~~~~~~~~~~~~~~~~~~~~~~

This bundle uses **Pagerfanta library** and **PagerfantaBundle**.

The installation guide can be found `here <https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle>`_.

Downloading the bundle
~~~~~~~~~~~~~~~~~~~~~~

The good practice is to download it to `vendor/bundles/Sylius/Bundle/AssortmentBundle`.

This can be done in several ways, depending on your preference.

The first method is the standard Symfony2 method.

Using the vendors script
************************

Add the following lines in your `deps` file. ::

    [SyliusAssortmentBundle]
        git=git://github.com/Sylius/SyliusAssortmentBundle.git
        target=bundles/Sylius/Bundle/AssortmentBundle

Now, run the vendors script to download the bundle.

.. code-block:: bash

    $ php bin/vendors install

Using submodules
****************

If you prefer instead to use git submodules, then run the following lines.

.. code-block:: bash

    $ git submodule add git://github.com/Sylius/SyliusAssortmentBundle.git vendor/bundles/Sylius/Bundle/AssortmentBundle
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
            new Sylius\Bundle\AssortmentBundle\SyliusAssortmentBundle(),
        );
    }

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now is the time to import routing files. Open up your `routing.yml` file. 

Customize the prefixes or whatever you want.

.. code-block:: yaml

    sylius_assortment_product:
        resource: @SyliusAssortmentBundle/Resources/config/routing/frontend/product.yml

    sylius_assortment_backend_category:
        resource: @SyliusAssortmentBundle/Resources/config/routing/backend/product.yml
        prefix: /administration/assortment/products

Creating your Product class
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Next step is creating your desired Product class. Its totally up to you how your product will look like so...

What are your waiting for?

.. note::

   We assume that **ApplicationAssortmentBundle** is your custom bundle enabled in the kernel!

.. code-block:: php

    <?php

    // src/Application/Bundle/AssortmentBundle/Entity/Product.php

    namespace Application\Bundle\AssortmentBundle\Entity;

    use Sylius\Bundle\AssortmentBundle\Entity\Product as BaseProduct;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_assortment_product")
     */
    class Product extends BaseProduct
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
    }

Container configuration
~~~~~~~~~~~~~~~~~~~~~~~

Now you have to do the minimal configuration, no worries, it is not painful.

Open up your `config.yml` file and add this...

.. code-block:: yaml

    sylius_assortment:
        driver: ORM
        classes:
            model:
                product: Application\Bundle\AssortmentBundle\Entity\Product

Please note, that the "**ORM**" is currently the only supported driver.

Updating database schema
~~~~~~~~~~~~~~~~~~~~~~~~

The last thing you need to do is updating the database schema.

For "**ORM**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

Usage guide
-----------

The bundle is shipped with nice default interface, it is usable right away.

Visit `localhost/administration/assortment/products/list` to see the list of products.

Form customization
~~~~~~~~~~~~~~~~~~

This is the simplest method to override default product form.

Create your form type class.

.. code-block:: php

    <?php

    namespace Application\Bundle\AssortmentBundle\Form\Type;

    use Sylius\Bundle\AssortmentBundle\Form\Type\ProductFormType as BaseProductFormType;
    use Symfony\Component\Form\FormBuilder;

    class ProductFormType extends BaseProductFormType
    {
        public function buildForm(FormBuilder $builder, array $options)
        {
            parent::buildForm($builder, $options);
            
            $builder
                ->add('reference', 'text')
                ->add('category', 'sylius_catalog_category_choice', array(
                    'multiple' => false,
                    'catalog_alias' => 'assortment'
                ))
            ;
        }
    }

Then put the class name in configuration.

.. code-block:: yaml

    sylius_assortment:
            driver: ORM
            classes:
                model:
                    product: # your product class.
                form:
                    type:
                        product: Sylius\Bundle\\AssortmentBundle\\Form\\Type\\ProductFormType

Events
~~~~~~

If you can do something without changing the manipulators, use the events system.

.. code-block:: php

    <?php

    // ...

    final class SyliusAssortmentEvents
    {
        const PRODUCT_CREATE = 'sylius_assortment.event.product.create';
        const PRODUCT_UPDATE = 'sylius_assortment.event.product.update';
        const PRODUCT_DELETE = 'sylius_assortment.event.product.delete';
    }

`Sylius\\Bundle\\AssortmentBundle\\EventDispatcher\\Event\\FilterProductEvent` class takes product 
instance as constructor argument.

Configuration reference
-----------------------

This is full bundle configuration.

.. code-block:: yaml

    sylius_assortment:
            driver: ORM
            engine: twig # templating engine name.
            classes:
                model:
                    product: # your product class.
                controller:
                    backend:
                        product: Sylius\Bundle\\AssortmentBundle\\Controller\Backend\\ProductController
                    frontend:
                        product: Sylius\Bundle\\AssortmentBundle\\Controller\Frontend\\ProductController
                form:
                    type:
                        product: Sylius\Bundle\\AssortmentBundle\\Form\\Type\\ProductFormType
                manipulator:
                    product: Sylius\\Bundle\\AssortmentBundle\\Manipulator\\ProductManipulator
                inflector:
                    slugizer: Sylius\Bundle\\AssortmentBundle\\Inflector\\Slugizer
                
Testing and continous integration
----------------------------------

.. image:: http://travis-ci.org/Sylius/SyliusAssortmentBundle.png

This bundle uses `travis-ci.org <http://travis-ci.org/Sylius/SyliusAssortmentBundle>`_ for CI.

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

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusAssortmentBundle/issues>`_.
If you have found bug, please create an issue.
