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

+-----------------+--------------------------------+
| Taxonomy                                         |
+-----------------+--------------------------------+
| Attribute       | Description                    |
+=================+================================+
| id              | Unique id of the taxonomy      |
+-----------------+--------------------------------+
| name            | Name of the taxonomy           |
+-----------------+--------------------------------+
| root            | First, "root" Taxon            |
+-----------------+--------------------------------+
| createdAt       | Date when taxonomy was created |
+-----------------+--------------------------------+
| updatedAt       | Date of last update            |
+-----------------+--------------------------------+

Taxons
------

+-----------------+--------------------------------+
| Taxon                                            |
+-----------------+--------------------------------+
| Attribute       | Description                    |
+=================+================================+
| id              | Unique id of the taxon         |
+-----------------+--------------------------------+
| name            | Name of the taxon              |
+-----------------+--------------------------------+
| slug            | Urlized name                   |
+-----------------+--------------------------------+
| permalink       | Full permalink for given taxon |
+-----------------+--------------------------------+
| description     | Description of taxon           |
+-----------------+--------------------------------+
| taxonomy        | Taxonomy                       |
+-----------------+--------------------------------+
| parent          | Parent taxon                   |
+-----------------+--------------------------------+
| children        | Sub taxons                     |
+-----------------+--------------------------------+
| left            | Location within taxonomy       |
+-----------------+--------------------------------+
| right           | Location within taxonomy       |
+-----------------+--------------------------------+
| level           | How deep it is in the tree     |
+-----------------+--------------------------------+
| createdAt       | Date when taxon was created    |
+-----------------+--------------------------------+
| updatedAt       | Date of last update            |
+-----------------+--------------------------------+
