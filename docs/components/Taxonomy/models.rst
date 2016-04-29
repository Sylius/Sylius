Models
======

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
| permalink       | Full permalink for given taxon taken form the ``TaxonTranslation`` |
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
| createdAt       | Date when taxon was created                                        |
+-----------------+--------------------------------------------------------------------+
| updatedAt       | Date of last update                                                |
+-----------------+--------------------------------------------------------------------+

.. note::

    This model implements the :ref:`component_taxonomy_model_taxon-interface`.
    You will find more information about this model in `Sylius API Taxon`_.

.. _Sylius API Taxon: http://api.sylius.org/Sylius/Component/Taxonomy/Model/Taxon.html

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
| permalink       | Full permalink for given taxon     |
+-----------------+------------------------------------+
| description     | Description of taxon               |
+-----------------+------------------------------------+

.. note::

    This model implements the :ref:`component_taxonomy_model_taxon-translation-interface`.
    You will find more information about this model in `Sylius API TaxonTranslation`_.

.. _Sylius API TaxonTranslation: http://api.sylius.org/Sylius/Component/Taxonomy/Model/TaxonTranslation.html
