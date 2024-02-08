.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Taxonomy is a list constructed from individual Taxons. Taxonomy is a special case of Taxon itself (it has no parent).
All taxons can have many child taxons, you can define as many of them as you need.

Good examples of taxonomies are "Categories" and "Brands". Below you can see exemplary trees.

.. code-block:: text

    | Categories
    |\__T-Shirts
    |   |\__Men
    |    \__Women
    |\__Stickers
    |\__Mugs
     \__Books

    | Brands
    |\__SuperTees
    |\__Stickypicky
    |\__Mugland
     \__Bookmania

.. _component_taxonomy_model_taxon:

Taxon
-----

+-----------------+--------------------------------------------------------------------+
| Property        | Description                                                        |
+=================+====================================================================+
| id              | Unique id of the taxon                                             |
+-----------------+--------------------------------------------------------------------+
| code            | Unique code of the taxon                                           |
+-----------------+--------------------------------------------------------------------+
| name            | Name of the taxon taken form the ``TaxonTranslation``              |
+-----------------+--------------------------------------------------------------------+
| slug            | Urlized name taken from the ``TaxonTranslation``                   |
+-----------------+--------------------------------------------------------------------+
| description     | Description of taxon taken from the ``TaxonTranslation``           |
+-----------------+--------------------------------------------------------------------+
| parent          | Parent taxon                                                       |
+-----------------+--------------------------------------------------------------------+
| children        | Sub taxons                                                         |
+-----------------+--------------------------------------------------------------------+
| left            | Location within taxonomy                                           |
+-----------------+--------------------------------------------------------------------+
| right           | Location within taxonomy                                           |
+-----------------+--------------------------------------------------------------------+
| level           | How deep it is in the tree                                         |
+-----------------+--------------------------------------------------------------------+
| position        | Position of the taxon on its taxonomy                              |
+-----------------+--------------------------------------------------------------------+

.. note::

    This model implements the :ref:`component_taxonomy_model_taxon-interface`.

.. _component_taxonomy_model_taxon-translation:

TaxonTranslation
----------------

This model stores translations for the **Taxon** instances.

+-----------------+------------------------------------+
| Property        | Description                        |
+=================+====================================+
| id              | Unique id of the taxon translation |
+-----------------+------------------------------------+
| name            | Name of the taxon                  |
+-----------------+------------------------------------+
| slug            | Urlized name                       |
+-----------------+------------------------------------+
| description     | Description of taxon               |
+-----------------+------------------------------------+

.. note::

    This model implements the :ref:`component_taxonomy_model_taxon-translation-interface`.
