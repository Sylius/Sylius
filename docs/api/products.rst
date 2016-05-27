Products API
============

Sylius products catalogue API endpoint is ``/api/products`` and it allows for browsing, creating & editing product information.

Index of all products
---------------------

To browse all products available in the store you should call the following GET request:

.. code-block:: text

    GET /api/products/

Parameters
~~~~~~~~~~

page
    Number of the page, by default = 1
limit
    Number of items to display per page
criteria[channel]
    Id of the channel *(optional)*
criteria[name]
    Name of the product *(optional)*

Response
~~~~~~~~

Response will contain a paginated list of products.

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page":1,
        "limit":10,
        "pages":12,
        "total":120,
        "_links":{
            "self":{
                "href":"\/api\/products\/?page=1"
            },
            "first":{
                "href":"\/api\/products\/?page=1"
            },
            "last":{
                "href":"\/api\/products\/?page=12"
            },
            "next":{
                "href":"\/api\/products\/?page=2"
            }
        },
        "_embedded":{
            "items":[
                {
                    "created_at": "2014-11-26T23:00:20+0000",
                    "description": "Facere ipsum id eveniet rem omnis et. Totam vero eos eveniet nihil sint. Labore occaecati qui placeat fugit.",
                    "id": 2173,
                    "name": "T-Shirt \"ipsam\"",
                    "code": "T-SHIRT-IPSAM",
                    "short_description": "Aut rerum quasi neque.",
                    "updated_at": "2014-11-26T23:00:20+0000"
                }
            ]
        }
    }

Getting a single product
------------------------

You can view a single product by executing the following request:

.. code-block:: text

    GET /api/products/2173

Response
~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "created_at": "2014-11-26T23:00:20+0000",
        "description": "Facere ipsum id eveniet rem omnis et. Totam vero eos eveniet nihil sint. Labore occaecati qui placeat fugit.",
        "id": 2173,
        "name": "T-Shirt \"ipsam\"",
        "code": "T-SHIRT-IPSAM",
        "short_description": "Aut rerum quasi neque.",
        "updated_at": "2014-11-26T23:00:20+0000"
    }

Creating an product
-------------------

To create a new product, you can execute the following request:

.. code-block:: text

    POST /api/products/

Parameters
~~~~~~~~~~

name
    Name of the product
description
    Description of the product
price
    Price of the product
shortDescription *(optional)*
    Short description of the product (for lists)

Response
~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "created_at": "2014-11-29T14:23:57+0000",
        "description": "Bar",
        "id": 2181,
        "name": "Foo",
        "code": "FOO",
        "updated_at": "2014-11-29T14:23:58+0000"
    }

Updating a product
------------------

You can update an existing product using PUT or PATCH method:

.. code-block:: text

    PUT /api/products/2181

.. code-block:: text

    PATCH /api/products/2181

Parameters
~~~~~~~~~~

name
    Name of the product
description
    Description of the product
price
    Price of the product
shortDescription *(optional)*
    Short description of the product (for lists)

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT

Deleting a product
------------------

You can delete (soft) a product from the catalog by making the following DELETE call:

.. code-block:: text

    DELETE /api/products/24

Response
~~~~~~~~

.. code-block:: text

    STATUS: 204 NO CONTENT
