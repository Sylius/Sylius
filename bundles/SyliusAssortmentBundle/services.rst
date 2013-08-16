Using the services
==================

When using the bundle, you have access to several handy services.
You can use them to manipulate and manage your assortment.

Managers and Repositories
-------------------------

.. note::

    Sylius uses ``Doctrine\Common\Persistence`` interfaces.

You have access to the following services which are used to manage and retrieve resources.

This set of default services is shared across almost all Sylius bundles, but this is just a convention.
You're interacting with them like you usually do with own entities in your project.

.. code-block:: php

    <?php

    // ...
    public function saveAction(Request $request)
    {
        // ObjectManager which is capable of managing the Product resource.
        // For *doctrine/orm* driver it will be EntityManager.
        $this->get('sylius.manager.product');

        // ObjectRepository for the Product resource, it extends the base EntityRepository.
        // You can use it like usual entity repository in project.
        $this->get('sylius.repository.product');

        // Same pair for other resources: variant, property, option, prototype.
        $this->get('sylius.manager.variant');
        $this->get('sylius.repository.variant');
        // ...

        // Those repositories have some handy default methods, for example...
        $product = $this->get('sylius.repository.product')->createNew();
    }

ProductBuilder
--------------

This service provides a fluent interface for easy product creation.

Example is self explanatory:

.. code-block:: php

    <?php

    $product = $this->get('sylius.product_builder')
        ->create('Github mug')
        ->setDescription("Coffee. Tea. Coke. Water. Let's face it — humans need to drink liquids")
        ->setPrice(12.00)
        ->addProperty('collection', 2013)
        ->addOption('size', array('S', 'M', 'L'))
        ->save()
    ;

PrototypeBuilder
----------------

Used to build product based on given prototype.

Here is an example:

.. code-block:: php

    <?php

    $prototype = $this->findOr404(array('id' => $prototypeId));
    $product = $this->get('sylius.repository.product')->createNew();

    $this
        ->get('sylius.prototype_builder')
        ->build($prototype, $product)
    ;

It will add appropriate options and variants to given product based on prototype.

VariantGenerator
----------------

Creates all possible combinations of product options and creates `Variant` models from them, directly on the product.
If product has two options with 3 possible values each, this service will create 9 variants and assign them on the product.
It ignores existing and invalid variants.

.. code-block:: php

    <?php

    $product = $this->findOr404(array('id' => $productId));

    $this
        ->get('sylius.variant_generator')
        ->generate($product)
    ;
