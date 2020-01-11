Customizing Factories
=====================

.. warning::

    Some factories may already be decorated in the **Sylius** Core.
    You need to check before decorating which factory (Component or Core) is your resource using.

Why would you customize a Factory?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Differently configured versions of resources may be needed in various scenarios in your application.
You may need for instance to:

    * create a Product with a Supplier (which is your own custom entity)
    * create a disabled Product (for further modifications)
    * create a ProductReview with predefined description

and many, many more.

How to customize a Factory?
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/12>`_

Let's assume that you would want to have a possibility to create disabled products.

**1.** Create your own factory class in the ``App\Factory`` namespace.
Remember that it has to implement a proper interface. How can you check that?

For the ``ProductFactory`` run:

.. code-block:: bash

    $ php bin/console debug:container sylius.factory.product

As a result you will get the ``Sylius\Component\Product\Factory\ProductFactory`` - this is the class that you need to decorate.
Take its interface (``Sylius\Component\Product\Factory\ProductFactoryInterface``) and implement it.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Factory;

    use Sylius\Component\Product\Model\ProductInterface;
    use Sylius\Component\Product\Factory\ProductFactoryInterface;

    final class ProductFactory implements ProductFactoryInterface
    {
        /** @var ProductFactoryInterface  */
        private $decoratedFactory;

        public function __construct(ProductFactoryInterface $factory)
        {
            $this->decoratedFactory = $factory;
        }

        public function createNew(): ProductInterface
        {
            return $this->decoratedFactory->createNew();
        }

        public function createWithVariant(): ProductInterface
        {
            return $this->decoratedFactory->createWithVariant();
        }

        public function createDisabled(): ProductInterface
        {
            /** @var ProductInterface $product */
            $product = $this->decoratedFactory->createWithVariant();

            $product->setEnabled(false);

            return $product;
        }
    }

**2.** In order to decorate the base ProductFactory with your implementation you need to configure it
as a decorating service in the ``config/services.yaml``.

.. code-block:: yaml

    services:
        app.factory.product:
            class: App\Factory\ProductFactory
            decorates: sylius.factory.product
            arguments: ['@app.factory.product.inner']
            public: false

**3.** You can use the new method of the factory in routing.

After the ``sylius.factory.product`` has been decorated it has got the new ``createDisabled()`` method.
To actually use it overwrite ``sylius_admin_product_create_simple`` route like below in ``config/routes.yaml``:

.. code-block:: yaml

    # config/routes.yaml
    sylius_admin_product_create_simple:
        path: /products/new/simple
        methods: [GET, POST]
        defaults:
            _controller: sylius.controller.product:createAction
            _sylius:
                section: admin
                factory:
                    method: createDisabled # like here for example
                template: SyliusAdminBundle:Crud:create.html.twig
                redirect: sylius_admin_product_update
                vars:
                    subheader: sylius.ui.manage_your_product_catalog
                    templates:
                        form: SyliusAdminBundle:Product:_form.html.twig
                    route:
                        name: sylius_admin_product_create_simple

.. include:: /customization/plugins.rst.inc

Learn more
----------

* :doc:`SyliusResourceBundle creating resources </components_and_bundles/bundles/SyliusResourceBundle/create_resource>`
