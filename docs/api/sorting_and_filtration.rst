Sorting and filtration
======================

In the Sylius API, a list of resources can be sorted and filtered by passed url query parameters. Here you can find examples how to
do it with sample resources.

.. note::

    To find out by which fields the api resources can be sorted and how they can be filtered you should check the grid configuration of these
    `here <https://github.com/Sylius/Sylius/tree/master/src/Sylius/Bundle/AdminApiBundle/Resources/config/grids>`_

How to sort resources?
----------------------

Let's assume that you want to sort products by code in descending order. In this case you should call
the ``/api/v1/products/`` endpoint with the ``GET`` method and provide sorting query parameters.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/?sorting\[{nameOfField}\]={direction}'

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| nameOfField   | query          | *(required)* Name of field by which the resource will be sorted   |
+---------------+----------------+-------------------------------------------------------------------+
| direction     | query          | *(required)* Define a direction of ordering                       |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

    $  curl 'http://demo.sylius.org/api/v1/products/?sorting\[code\]=desc&limit=4' \
          -H "Authorization: Bearer SampleToken"

Exemplary response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 4,
        "pages": 15,
        "total": 60,
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "first": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=1&limit=4"
            },
            "last": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=15&limit=4"
            },
            "next": {
                "href": "\/api\/v1\/products\/?sorting%5Bcode%5D=desc&page=2&limit=4"
            }
        },
        "_embedded": {
            "items": [
                {
                    "name": "Book \"facilis\" by Deborah Schmitt",
                    "id": 32,
                    "code": "fe1a18b9-f67a-35fb-bc64-78a60c724493",
                    "options": [],
                    "averageRating": 3,
                    "images": [
                        {
                            "id": 63,
                            "type": "main"
                        },
                        {
                            "id": 64,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/fe1a18b9-f67a-35fb-bc64-78a60c724493"
                        }
                    }
                },
                {
                    "name": "Book \"voluptate\" by Jazlyn Casper",
                    "id": 39,
                    "code": "f9d5ae66-6c1d-361b-a22d-28ed4bc8a10e",
                    "options": [],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 77,
                            "type": "main"
                        },
                        {
                            "id": 78,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/f9d5ae66-6c1d-361b-a22d-28ed4bc8a10e"
                        }
                    }
                },
                {
                    "name": "Mug \"veniam\"",
                    "id": 5,
                    "code": "f64f7c29-1128-3d12-93d1-19932345b83d",
                    "options": [
                        {
                            "id": 1,
                            "code": "mug_type",
                            "position": 0,
                            "values": [
                                {
                                    "code": "mug_type_medium",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 1,
                                            "value": "Medium mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_double",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 2,
                                            "value": "Double mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_monster",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 3,
                                            "value": "Monster mug"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/mug_type"
                                }
                            }
                        }
                    ],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 9,
                            "type": "main"
                        },
                        {
                            "id": 10,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/f64f7c29-1128-3d12-93d1-19932345b83d"
                        }
                    }
                },
                {
                    "name": "Sticker \"animi\"",
                    "id": 22,
                    "code": "e77f129f-5921-3ad2-88bd-f27b59aad037",
                    "options": [
                        {
                            "id": 2,
                            "code": "sticker_size",
                            "position": 1,
                            "values": [
                                {
                                    "code": "sticker_size-3",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 4,
                                            "value": "3\""
                                        }
                                    }
                                },
                                {
                                    "code": "sticker_size_5",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 5,
                                            "value": "5\""
                                        }
                                    }
                                },
                                {
                                    "code": "sticker_size_7",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 6,
                                            "value": "7\""
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/sticker_size"
                                }
                            }
                        }
                    ],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 43,
                            "type": "main"
                        },
                        {
                            "id": 44,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/e77f129f-5921-3ad2-88bd-f27b59aad037"
                        }
                    }
                }
            ]
        }
    }

How to filter resources?
------------------------

Let's assume that you want to find all products which contain the word ``sticker`` in the name. In this case you should call
the ``/api/v1/products/`` endpoint with the ``GET`` method and provide filter query parameters.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/?criteria\[{nameOfCriterion}\]\[type\]={searchOption}&criteria\[{nameOfCriterion}\]\[value\]={searchPhrase}'

+-----------------+----------------+--------------------------------------------------------------------------+
| Parameter       | Parameter type | Description                                                              |
+=================+================+==========================================================================+
| Authorization   | header         | Token received during authentication                                     |
+-----------------+----------------+--------------------------------------------------------------------------+
| nameOfCriterion | query          | *(required)* The name of criterion (for example "search", "couponBased") |
+-----------------+----------------+--------------------------------------------------------------------------+
| searchPhrase    | query          | *(required)* The searching phrase                                        |
+-----------------+----------------+--------------------------------------------------------------------------+
| searchOption    | query          | *(required)* Option of searching (for example "contains", "equal")       |
+-----------------+----------------+--------------------------------------------------------------------------+
| limit           | query          | *(optional)* Number of items to display per page, by default = 10        |
+-----------------+----------------+--------------------------------------------------------------------------+

.. note::

    The *nameOfCriterion* is a key from the grid configuration of a sample resource.

.. tip::

    You can find a list of all search options in :doc:`GridBundle docs</bundles/SyliusGridBundle/filters>`.

Example
^^^^^^^

.. code-block:: bash

    $  curl 'http://demo.sylius.org/api/v1/products/?criteria\[search\]\[type\]=contains&criteria\[search\]\[value\]=sticker&limit=4' \
          -H "Authorization: Bearer SampleToken"

Exemplary response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 4,
        "pages": 15,
        "total": 60,
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/?criteria%5C%5Bsearch%5C%5D=sticker&page=1&limit=4"
            },
            "first": {
                "href": "\/api\/v1\/products\/?criteria%5C%5Bsearch%5C%5D=sticker&page=1&limit=4"
            },
            "last": {
                "href": "\/api\/v1\/products\/?criteria%5C%5Bsearch%5C%5D=sticker&page=15&limit=4"
            },
            "next": {
                "href": "\/api\/v1\/products\/?criteria%5C%5Bsearch%5C%5D=sticker&page=2&limit=4"
            }
        },
        "_embedded": {
            "items": [
                {
                    "name": "Book \"voluptates\" by Eveline Waters",
                    "id": 35,
                    "code": "00ebc508-48f5-326e-8f71-81e4feb0da73",
                    "options": [],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 69,
                            "type": "main"
                        },
                        {
                            "id": 70,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/00ebc508-48f5-326e-8f71-81e4feb0da73"
                        }
                    }
                },
                {
                    "name": "Mug \"voluptatibus\"",
                    "id": 7,
                    "code": "0bd9c774-d659-37b7-a22e-44615c155633",
                    "options": [
                        {
                            "id": 1,
                            "code": "mug_type",
                            "position": 0,
                            "values": [
                                {
                                    "code": "mug_type_medium",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 1,
                                            "value": "Medium mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_double",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 2,
                                            "value": "Double mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_monster",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 3,
                                            "value": "Monster mug"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/mug_type"
                                }
                            }
                        }
                    ],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 13,
                            "type": "main"
                        },
                        {
                            "id": 14,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/0bd9c774-d659-37b7-a22e-44615c155633"
                        }
                    }
                },
                {
                    "name": "Mug \"neque\"",
                    "id": 10,
                    "code": "13ad9ca9-8948-371b-b5b6-d2d988748b07",
                    "options": [
                        {
                            "id": 1,
                            "code": "mug_type",
                            "position": 0,
                            "values": [
                                {
                                    "code": "mug_type_medium",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 1,
                                            "value": "Medium mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_double",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 2,
                                            "value": "Double mug"
                                        }
                                    }
                                },
                                {
                                    "code": "mug_type_monster",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 3,
                                            "value": "Monster mug"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/mug_type"
                                }
                            }
                        }
                    ],
                    "averageRating": 0,
                    "images": [
                        {
                            "id": 19,
                            "type": "main"
                        },
                        {
                            "id": 20,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/13ad9ca9-8948-371b-b5b6-d2d988748b07"
                        }
                    }
                },
                {
                    "name": "T-Shirt \"a\"",
                    "id": 56,
                    "code": "1823af3c-184a-359d-9c05-6417c7e6abe0",
                    "options": [
                        {
                            "id": 3,
                            "code": "t_shirt_color",
                            "position": 2,
                            "values": [
                                {
                                    "code": "t_shirt_color_red",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 7,
                                            "value": "Red"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_color_black",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 8,
                                            "value": "Black"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_color_white",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 9,
                                            "value": "White"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/t_shirt_color"
                                }
                            }
                        },
                        {
                            "id": 4,
                            "code": "t_shirt_size",
                            "position": 3,
                            "values": [
                                {
                                    "code": "t_shirt_size_s",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 10,
                                            "value": "S"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_m",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 11,
                                            "value": "M"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_l",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 12,
                                            "value": "L"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_xl",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 13,
                                            "value": "XL"
                                        }
                                    }
                                },
                                {
                                    "code": "t_shirt_size_xxl",
                                    "translations": {
                                        "en_US": {
                                            "locale": "en_US",
                                            "id": 14,
                                            "value": "XXL"
                                        }
                                    }
                                }
                            ],
                            "_links": {
                                "self": {
                                    "href": "\/api\/v1\/products\/t_shirt_size"
                                }
                            }
                        }
                    ],
                    "averageRating": 3,
                    "images": [
                        {
                            "id": 111,
                            "type": "main"
                        },
                        {
                            "id": 112,
                            "type": "thumbnail"
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/1823af3c-184a-359d-9c05-6417c7e6abe0"
                        }
                    }
                }
            ]
        }
    }
