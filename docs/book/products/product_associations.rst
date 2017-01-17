.. index::
   single: Product Associations

Product Associations
====================

Associations of products can be used as a marketing tool for suggesting your customers, what products to buy together with
the one they are currently considering.
Associations can increase your shop's efficiency. You choose what strategy you prefer. They are fully configurable.

Association Types
-----------------

The type of an association can be different. If you sell food you can suggest inspiring ingredients, if you sell products
for automotive you can suggest buying some tools that may be useful for a home car mechanic.
Exemplary association types can be: ``up-sell``, ``cross-sell``, ``accessories``, ``alternatives`` and whatever you imagine.

How to create a new Association Type?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Create a new Association Type using a dedicated factory. Give the association a ``code`` and a ``name`` to easily recognize the type.

.. code-block:: php

    /** @var ProductAssociationTypeInterface $associationType */
    $associationType = $this->container->get('sylius.factory.product_association_type')->createNew();

    $associationType->setCode('accessories');
    $associationType->setName('Accessories');

To have the new association type in the system add it to the repository.

.. code-block:: php

    $this->container->get('sylius.repository.product_association_type')->add($associationType);

How to add a new Association to a Product?
------------------------------------------

Find in your system a product to which you would like to add an association. We will use a Go Pro camera as an example.

.. code-block:: php

    $product = $this->container->get('sylius.repository.product')->findOneBy(['code' => 'go-pro-camera']);

Next create a new Association which will connect our camera with its accessories. Such an association needs the AssociationType we have created
in the previous step above.

.. code-block:: php

    /** @var ProductAssociationInterface $association */
    $association = $this->container->get('sylius.factory.product_association')->createNew();

    /** @var ProductAssociationTypeInterface $associationType */
    $associationType = $this->container->get('sylius.repository.product_association_type')->findOneBy(['code' => 'accessories']);

    $association->setType($associationType);

Let's add all products from a certain taxon to the association we have created.
To do that find a desired taxon by code and get all its products. Perfect accessories for a camera will be SD cards.

.. code-block:: php

    /** @var TaxonInterface $taxon */
    $taxon = $this->container->get('sylius.repository.taxon')->findOneBy(['code' => 'sd-cards']);

    $associatedProducts = $taxon->getProducts();

Having a collection of products from the SD cards taxon iterate over them and add them one by one to the association.

.. code-block:: php

    foreach ($associatedProducts as $associatedProduct) {
        $association->addAssociatedProduct($associatedProduct);
    }

Finally add the created association with SD cards to our Go Pro camera product.

.. code-block:: php

    $product->addAssociation($association);

And to save everything in the database you need to add the created association to the repository.

.. code-block:: php

    $this->container->get('sylius.repository.product_association')->add($association);

Learn more:
-----------

* :doc:`Product - Concept Documentation </book/products/products>`
