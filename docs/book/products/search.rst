.. index::
   single: Search

Search
======

Having a products search functionality in an eCommerce system is a very popular use case.
Sylius provides a products search functionality that is a grid filter.

Grid filters
------------

For simple use cases of products search use the **filters of grids**.
For example, the shop's categories each have a ``search`` filter in the products grid:

.. code-block:: yaml

    # Sylius/Bundle/ShopBundle/Resources/config/grids/product.yml
    filters:
        search:
            type: string
            label: false
            options:
                fields: [translation.name]
            form_options:
                type: contains

It searches by product names that contains a string that the user typed in the search bar.

The search bar looks like below:

.. image:: ../../_images/search.png
    :align: center

Customizing search filter
^^^^^^^^^^^^^^^^^^^^^^^^^

The search bar in many shops should be more sophisticated, than just a simple text search. You may need to add
searching by price, reviews, sizes or colors.

If you would like to extend this built-in functionality read
:doc:`this article about grids customizations </customization/grid>`, and :doc:`the GridBundle docs </components_and_bundles/bundles/SyliusGridBundle/index>`.

ElasticSearch
-------------

When the grids filtering is not enough for you, and your needs are more complex you should go for the
`ElasticSearch <https://www.elastic.co/products/elasticsearch>`_ integration.

There is the `Sylius/SyliusElasticSearchPlugin <https://github.com/Sylius/SyliusElasticSearchPlugin>`_ integration extension,
which you can use to extend Sylius functionalities with ElasticSearch.

All you have to do is require the plugin in your project via composer, install the ElasticSearch server, and configure ElasticSearch
in your application. Everything is well described in the Sylius/SyliusElasticSearchPlugin's readme.

Learn more
----------

* `SyliusElasticSearchPlugin <https://github.com/Sylius/SyliusElasticSearchPlugin>`_
* :doc:`Grid Bundle documentation </components_and_bundles/bundles/SyliusGridBundle/index>`
* :doc:`Grid Component documentation </components_and_bundles/components/Grid/index>`
