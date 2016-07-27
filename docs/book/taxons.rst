.. index::
   single: Taxons

Taxons
======

We understand Taxons in Sylius as you would normally define categories.
Sylius gives you a possibility to categorize your products in a very flexible way, which is one of the most vital funtionalities
of the modern e-commerce systems.
The Taxons system in Sylius works in a hierarchical way.
Let's see exemplary categories trees:

.. code-block:: php

    Category
     |
     |\__ Clothes
     |         \_ T-Shirts
     |          \_ Shirts
     |           \_ Dresses
     |            \_ Shoes
     |
     \__ Books
             \_ Fantasy
              \_ Romance
               \_ Adventure
                \_ Other

    Gender
     |
     \_ Male
      \_ Female

How to create a Taxon?
----------------------

As always with Sylius resources, to create a new object you need a factory.
If you want to create a single, not nested category:

.. code-block:: php

     /** @var FactoryInterface $taxonFactory */
     $taxonFactory = $this->get('sylius.factory.taxon');

     /** @var TaxonInterface $taxon */
     $taxon = $taxonFactory->createNew();

     $taxon->setCode('category');
     $taxon->setName('Category');

But if you want to have a tree of categories, create another taxon and add it as a **child** to the previously created one.

.. code-block:: php

     /** @var TaxonInterface $childTaxon */
     $childTaxon = $taxonFactory->createNew();

     $childTaxon->setCode('clothes');
     $childTaxon->setName('Clothes');

     $childTaxon->setParent($taxon);
     $taxon->addChild($childTaxon);

Finally **the parent taxon** has to be added to the system using a repository, all its child taxons will be added with it.

.. code-block:: php

     /** @var TaxonRepositoryInterface $taxonsRepository */
     $taxonRepository = $this->get('sylius.repository.taxon');

     $taxonRepository->add($taxon);

Learn more
----------

* :doc:`Taxonomy - Bundle Documentation </bundles/SyliusTaxonomyBundle/index>`
* :doc:`taxonomy - Component Documentation </components/Taxonomy/index>`
