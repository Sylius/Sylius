Customizing Factories
=====================

.. warning::

    In **Sylius** we are already decorating factories from components in Core.
    Often you will be needing to add your very own methods to them. You need to check before which factory is your resource using.

Why would you customize a Factory?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Differently configured versions of resources may be needed in various scenarios in your application.
You may need for instance to:

    * create Product with Supplier(which is your own custom entity)
    * create a disabled Product(for further modifications)
    * create a ProductReview with predefined description

and many, many more usecases.

How to customize a Factory?
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would want to have a possibility to create disabled products.

1. Create your own factory class in the ``AppBundle\Factory`` namespace.
Remember that it has to extend a proper base class. How can you check that?

For the ``ProductFactory`` run:

.. code-block:: bash

    $ php bin/console debug:container sylius.factory.product

As a result you will get the ``Sylius\Component\Product\Factory\ProductFactory`` - this is the class that you need to be extending.

.. code-block:: php

    <?php

    namespace AppBundle\Factory;

    use Sylius\Component\Core\Model\AddressInterface;
    use Sylius\Component\Core\Model\ProductInterface;
    use Sylius\Component\Product\Factory\ProductFactory as BaseProductFactory;
    use Sylius\Component\Core\Model\CustomerInterface;
    use Sylius\Component\Resource\Factory\FactoryInterface;

    class ProductFactory extends BaseProductFactory
    {
        /**
         * @var FactoryInterface
         */
        private $factory;

        /**
         * @var FactoryInterface
         */
        private $variantFactory;

        /**
         * @param FactoryInterface $factory
         * @param FactoryInterface $variantFactory
         */
        public function __construct(FactoryInterface $factory, FactoryInterface $variantFactory)
        {
            parent::__construct($factory, $variantFactory);

            $this->factory = $factory;
            $this->variantFactory = $variantFactory;
        }

        /**
         * @return ProductInterface
         */
        public function createDisabled()
        {
            /** @var ProductInterface $product */
            $product = $this->factory->createNew();

            $product->setEnabled(false);

            return $product;
        }
    }

2. In order to decorate the base ProductFactory with your implementation you need to configure it
as a decorating service in the ``AppBundle\Resources\config\services.yml``.

.. code-block:: yaml

    services:
        app.factory.product:
            class: AppBundle\Factory\ProductFactory
            decorates: sylius.factory.product
            arguments: ['@app.factory.product.inner']
            public: false

3. After the ``sylius.factory.product`` has been decorated it has been extended by the new ``createDisabled()`` method.

You can use the new method of factory in routing.

.. code-block:: yaml

    sylius_admin_product_create_simple:
        path: /products/new/simple
        methods: [GET, POST]
        defaults:
            _controller: sylius.controller.product:createAction
            _sylius:
                section: admin
                factory:
                    method: createDisable # like here for example
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
