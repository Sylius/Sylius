Prototypes
==========

Product is the main model in SyliusProductBundle. This simple class represents every unique product in the catalog.
Default interface contains following attributes with appropriate setters and getters.

+-----------------+----------------------------------------------------+
| Attribute       | Description                                        |
+=================+====================================================+
| id              | Unique id of the product                           |
+-----------------+----------------------------------------------------+
| name            | Name of the product                                |
+-----------------+----------------------------------------------------+
| slug            | SEO slug, by default created from the name         |
+-----------------+----------------------------------------------------+
| description     | Description of your great product                  |
+-----------------+----------------------------------------------------+
| availableOn     | Date when product becomes available in catalog     |
+-----------------+----------------------------------------------------+
| metaDescription | Description for search engines                     |
+-----------------+----------------------------------------------------+
| metaKeywords    | Comma separated list of keywords for product (SEO) |
+-----------------+----------------------------------------------------+
| createdAt       | Date when product was created                      |
+-----------------+----------------------------------------------------+
| updatedAt       | Date of last product update                        |
+-----------------+----------------------------------------------------+
| deletedAt       | Date of deletion from catalog                      |
+-----------------+----------------------------------------------------+

Prototype Builder
-----------------

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
