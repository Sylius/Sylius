The Object Model
================

Here is a quick reference of what the default models can do for you.

Taxonomy
--------

Taxonomy is a collection of taxons. It has a name and is used to group taxons.
You can have taxonomies like category, brand...

Taxon
-----

Taxon is a child node which exists at a given point within a taxonomy.
Each taxon can contain many sub-child taxons, so it is implemented as a tree structure.

.. code-block:: text

    | Caterory
    |--  T-Shirts
    |--  Stickers
    |    |-- Men
    |    `-- Women
    |--  Mugs
    `--  Books

    | Brands
    |-- SuperTees
    |-- Stickypicky
    |-- Mugland
    `-- Bookmania
