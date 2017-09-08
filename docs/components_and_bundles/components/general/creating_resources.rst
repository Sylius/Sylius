Creating Resources
==================

Every resource provided by Sylius component should be created via factory.

Some of them can use default factory class and some use custom implementation to provide extra functionality.

Using Factory To Create New Resource
------------------------------------

To create new resources you should use default factory implementation.

.. code-block:: php

    <?php

    use Sylius\Component\Product\Model\Product;
    use Sylius\Component\Resource\Factory\Factory;

    $factory = new Factory(Product::class);

    $product = $factory->createNew();

That's it! ``$product`` variable will hold a clean instance of the Product model.

Why Even Bother?
----------------

"Hey! This is same as ``$product = new Product();``!"

Yes, and no. Every Factory implements `FactoryInterface`_ and this allows to abstract the way that resources are created.
It also makes testing much simpler because you can mock the Factory and use it as a test double in your service.

What is more, thanks to usage of Factory pattern, Sylius is able to easily swap default Product (or any other resource) model with your custom implementation, without changing code.

.. _FactoryInterface: http://api.sylius.org/Sylius/Component/Resource/Factory/FactoryInterface.html

.. note::
    For more detailed information go to `Sylius API Factory`_.

.. _Sylius API Factory: http://api.sylius.org/Sylius/Component/Resource/Factory/Factory.html

.. caution::
    In concrete Component's documentation we will use ``new`` keyword to create resources - just to keep things simpler to read.
