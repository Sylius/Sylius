Product Builder
===============

This service provides a fluent interface for easy product creation.

The example shown below is self explanatory:

.. code-block:: php

    <?php

    $product = $this->get('sylius.builder.product')
        ->create('Github mug')
        ->setDescription("Coffee. Let's face it — humans need to drink liquids!")
        ->addProperty('collection', 2013)
        ->addProperty('color', 'Red')
        ->addProperty('material', 'Stone')
        ->setPrice(1200)
        ->save()
    ;
