Product Properties
==================

Managing Properties
-------------------

Managing properties happens exactly the same way like products, you have ``sylius.repository.property`` and ``sylius.manager.property`` at your disposal.

Assigning properties to product
-------------------------------

Value of specific Property for one of Products, happens through ProductProperty model, which holds the references to Product, Property pair and the value.
If you want to programatically set a property value on product, use the following code.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $propertyRepository = $this->container->get('sylius.repository.property');
        $productPropertyRepository = $this->container->get('sylius.repository.product_property');

        $property = $propertyRepository->findOneBy(array('name' => 'T-Shirt Collection'));
        $productProperty = $productPropertyRepository->createNew();

        $productProperty
            ->setProperty($property)
            ->setValue('Summer 2013')
        ;

        $product->addProperty($productProperty);

        $manager = $this->container->get('sylius.manager.product');

        $manager->persist($product);
        $manager->flush(); // Save changes in database.
    }

This looks a bit tedious, doesn't it? There is a **ProductBuilder** service which simplifies the creation of products dramatically, you can learn about it in appropriate chapter.
