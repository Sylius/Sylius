Models
======

Taxonomy is a list constructed from individual Taxons. Every taxonomy has one special taxon, which serves as a root of the tree.
All taxons can have many child taxons, you can define as many of them as you need.

A good examples of taxonomies are "Categories" and "Brands". Below you can see an example tree.

.. code-block:: text

    | Categories
    |--  T-Shirts
    |    |-- Men
    |    `-- Women
    |--  Stickers
    |--  Mugs
    `--  Books

    | Brands
    |-- SuperTees
    |-- Stickypicky
    |-- Mugland
    `-- Bookmania

Taxonomy
--------

+-----------------+--------------------------------+------------------------+
| Attribute       | Description                    | Type                   |
+=================+================================+========================+
| id              | Unique id of the taxonomy      | mixed                  |
+-----------------+--------------------------------+------------------------+
| name            | Name of the taxonomy           | string                 |
+-----------------+--------------------------------+------------------------+
| root            | First, "root" Taxon            | TaxonInterface         |
+-----------------+--------------------------------+------------------------+
| createdAt       | Date when taxonomy was created | \DateTime              |
+-----------------+--------------------------------+------------------------+
| updatedAt       | Date of last update            | \DateTime              |
+-----------------+--------------------------------+------------------------+

This model implements ``TaxonomyInterface``, it implements these extra methods:

+------------------------------------+-------------------------------------+----------------------------+
| Method                             | Description                         | Returned value             |
+====================================+=====================================+============================+
| getTaxons()                        | Adds option value                   | TaxonInterface[]           |
+------------------------------------+-------------------------------------+----------------------------+
| hasTaxon(TaxonInterface $taxon)    | Check if the taxonomy has taxon     | boolean                    |
+------------------------------------+-------------------------------------+----------------------------+
| addTaxon(TaxonInterface $taxon)    | Add a taxon                         | Void                       |
+------------------------------------+-------------------------------------+----------------------------+
| removeTaxon(TaxonInterface $taxon) | Remove a taxon a taxon              | Void                       |
+------------------------------------+-------------------------------------+----------------------------+


Taxons
------

+-----------------+--------------------------------+------------------------+
| Attribute       | Description                    | Type                   |
+=================+================================+========================+
| id              | Unique id of the taxon         | mixed                  |
+-----------------+--------------------------------+------------------------+
| name            | Name of the taxon              | string                 |
+-----------------+--------------------------------+------------------------+
| slug            | Urlized name                   | string                 |
+-----------------+--------------------------------+------------------------+
| permalink       | Full permalink for given taxon | string                 |
+-----------------+--------------------------------+------------------------+
| description     | Description of taxon           | string                 |
+-----------------+--------------------------------+------------------------+
| taxonomy        | Taxonomy                       | TaxonomyInterface      |
+-----------------+--------------------------------+------------------------+
| parent          | Parent taxon                   | TaxonInterface         |
+-----------------+--------------------------------+------------------------+
| children        | Sub taxons                     | Collection             |
+-----------------+--------------------------------+------------------------+
| left            | Location within taxonomy       | mixed                  |
+-----------------+--------------------------------+------------------------+
| right           | Location within taxonomy       | mixed                  |
+-----------------+--------------------------------+------------------------+
| level           | How deep it is in the tree     | mixed                  |
+-----------------+--------------------------------+------------------------+
| createdAt       | Date when taxon was created    | \DateTime              |
+-----------------+--------------------------------+------------------------+
| updatedAt       | Date of last update            | \DateTime              |
+-----------------+--------------------------------+------------------------+

This model implements ``TaxonInterface``, it implements these extra methods:

+------------------------------------+-------------------------------------+----------------+
| Method                             | Description                         | Returned value |
+====================================+=====================================+================+
| hasChild()                         | Check whether the taxon has a child | boolean        |
+------------------------------------+-------------------------------------+----------------+
| addChild(TaxonInterface $taxon)    | Add child taxon                     | Void           |
+------------------------------------+-------------------------------------+----------------+
| removeChild(TaxonInterface $taxon) | Remove child taxon.                 | Void           |
+------------------------------------+-------------------------------------+----------------+

TaxonsAwareInterface
--------------------

This interface should be implemented by models that support taxons.

+------------------------------------+---------------------------------+--------------------+
| Method                             | Description                     | Returned value     |
+====================================+=================================+====================+
| getTaxons($taxonomy = null)        | Get all taxons                  | VariantInterface   |
+------------------------------------+---------------------------------+--------------------+
| setTaxons(Collection $collection)  | Set the taxons                  | Void               |
+------------------------------------+---------------------------------+--------------------+
| hasTaxon(TaxonInterface $taxon)    | Checks whether object has taxon | Boolean            |
+------------------------------------+---------------------------------+--------------------+
| addTaxon(TaxonInterface $taxon)    | Add a taxon                     | VariantInterface[] |
+------------------------------------+---------------------------------+--------------------+
| removeTaxon(TaxonInterface $taxon) | Remove a taxon                  | Void               |
+------------------------------------+---------------------------------+--------------------+