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

Let's assume that you would want to have a possibility to create disabled products.

**1.** Create your own factory class in the ``AppBundle\Factory`` namespace.
Remember that it has to implement a proper interface. How can you check that?

For the ``ProductFactory`` run:

.. code-block:: bash

    $ php bin/console debug:container sylius.factory.product

As a result you will get the ``Sylius\Component\Product\Factory\ProductFactory`` - this is the class that you need to decorate.
Take its interface (``Sylius\Component\Product\Factory\ProductFactoryInterface``) and implement it.

.. code-block:: php

    <?php

    namespace AppBundle\Factory;

    use Sylius\Component\Core\Model\ProductInterface;
    use Sylius\Component\Product\Factory\ProductFactoryInterface;

    class ProductFactory implements ProductFactoryInterface
    {
        /**
         * @var ProductFactoryInterface
         */
        private $decoratedFactory;

        /**
         * @param ProductFactoryInterface $factory
         */
        public function __construct(ProductFactoryInterface $factory)
        {
            $this->decoratedFactory = $factory;
        }

        /**
         * {@inheritdoc}
         */
        public function createNew()
        {
            return $this->decoratedFactory->createNew();
        }

        /**
         * {@inheritdoc}
         */
        public function createWithVariant()
        {
            return $this->decoratedFactory->createWithVariant();
        }

        /**
         * @return ProductInterface
         */
        public function createDisabled()
        {
            /** @var ProductInterface $product */
            $product = $this->decoratedFactory->createNew();

            $product->setEnabled(false);

            return $product;
        }
    }

**2.** In order to decorate the base ProductFactory with your implementation you need to configure it
as a decorating service in the ``app/Resources/config/services.yml``.

.. code-block:: yaml

    services:
        app.factory.product:
            class: AppBundle\Factory\ProductFactory
            decorates: sylius.factory.product
            arguments: ['@app.factory.product.inner']
            public: false

**3.** You can use the new method of the factory in routing.

After the ``sylius.factory.product`` has been decorated it has got the new ``createDisabled()`` method.
You can for example override ``sylius_admin_product_create_simple`` route like below:

.. code-block:: yaml

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

Learn more
----------

* :doc:`SyliusResourceBundle creating resources </bundles/SyliusResourceBundle/create_resource>`
