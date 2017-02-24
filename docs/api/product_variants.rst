Product Variants API
====================

These endpoints will allow you to easily manage product variants. Base URI is `/api/v1/products/{productCode}/variants/`.

Product Variant API response structure
--------------------------------------

When you get a collection of resources, "Default" serialization group will be used and the following fields will be exposed:

+------------------+------------------------------------------------------------------------------------------------+
| Field            | Description                                                                                    |
+==================+================================================================================================+
| id               | Id of product variant                                                                          |
+------------------+------------------------------------------------------------------------------------------------+
| code             | Unique product variant's identifier                                                            |
+------------------+------------------------------------------------------------------------------------------------+
| position         | Position of variant in product                                                                 |
|                  | (each product can have many variants and they can be ordered by position)                      |
+------------------+------------------------------------------------------------------------------------------------+
| optionValues     | Collection of options in which product is available (for example: small, medium and large mug) |
+------------------+------------------------------------------------------------------------------------------------+
| translations     | Collection of translations (each contains name in given language)                              |
+------------------+------------------------------------------------------------------------------------------------+
| tracked          | The information if the variant is tracked by inventory                                         |
+------------------+------------------------------------------------------------------------------------------------+
| channelPricings  | Collection of prices defined for all enabled channels                                          |
+------------------+------------------------------------------------------------------------------------------------+
| taxCategory      | Tax category to which variant is assigned                                                      |
+------------------+------------------------------------------------------------------------------------------------+
| shippingCategory | Shipping category to which variant is assigned                                                 |
+------------------+------------------------------------------------------------------------------------------------+

If you request more detailed data, you will receive an object with the following fields:

+------------------+------------------------------------------------------------------------------------------------+
| Field            | Description                                                                                    |
+==================+================================================================================================+
| id               | Id of product variant                                                                          |
+------------------+------------------------------------------------------------------------------------------------+
| code             | Unique product variant's identifier                                                            |
+------------------+------------------------------------------------------------------------------------------------+
| position         | Position of variant in product                                                                 |
|                  | (each product can have many variant and they can be ordered by position)                       |
+------------------+------------------------------------------------------------------------------------------------+
| tracked          | The information if the variant is tracked by inventory                                         |
+------------------+------------------------------------------------------------------------------------------------+
| channelPricings  | Collection of prices defined for all enabled channels                                          |
+------------------+------------------------------------------------------------------------------------------------+
| taxCategory      | Tax category to which variant is assigned                                                      |
+------------------+------------------------------------------------------------------------------------------------+
| shippingCategory | Shipping category to which variant is assigned                                                 |
+------------------+------------------------------------------------------------------------------------------------+
| optionValues     | Collection of options in which product is available (for example: small, medium and large mug) |
+------------------+------------------------------------------------------------------------------------------------+
| translations     | Collection of translations (each contains name in given language)                              |
+------------------+------------------------------------------------------------------------------------------------+
| onHold           | Information about how many product are currently reserved by customer                          |
+------------------+------------------------------------------------------------------------------------------------+
| onHand           | Information about the number of product in given variant currently available in shop           |
+------------------+------------------------------------------------------------------------------------------------+
| width            | The physical width of variant                                                                  |
+------------------+------------------------------------------------------------------------------------------------+
| height           | The physical height of variant                                                                 |
+------------------+------------------------------------------------------------------------------------------------+
| depth            | The physical depth of variant                                                                  |
+------------------+------------------------------------------------------------------------------------------------+
| weight           | The physical weight of variant                                                                 |
+------------------+------------------------------------------------------------------------------------------------+

.. note::

    Read more about :doc:`ProductVariant model in the component docs</components/Product/models>`.

Creating a Product Variant
--------------------------

To create a new product variant you will need to call the ``/api/v1/products/productCode/variants/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/products/{productCode}/variants/

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| productCode   | url attribute  | Id of product for which the variants should be displayed |
+---------------+----------------+----------------------------------------------------------+
| code          | request        | **(unique)** Product variant identifier                  |
+---------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To create new product variant for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "code": "medium-mug-theme"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 334,
        "code": "medium-mug-theme",
        "optionValues": [],
        "position": 0,
        "translations": [],
        "onHold": 0,
        "onHand": 0,
        "tracked": false,
        "channelPricings": [],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/MUG-TH\/variants\/medium-mug-theme"
            },
            "product": {
                "href": "\/api\/v1\/products\/MUG-TH"
            }
        }
    }

.. note::

    Be sure that the product for which you want to create a new variant is enabled.

.. warning::

    If you try to create a resource without code, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code":400,
        "message":"Validation Failed",
        "errors": {
            "children": {
                "enabled":{},
                "translations":{},
                "attributes":{},
                "associations":{},
                "channels":{},
                "mainTaxon":{},
                "productTaxons":{},
                "images":{},
                "code":{
                    "errors":["Please enter product code."]
                },
                "options":{}
            }
        }
    }

You can also create a product variant with additional (not required) fields:

+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| Parameter                         | Parameter type | Description                                                                                                 |
+===================================+================+=============================================================================================================+
| Authorization                     | header         | Token received during authentication                                                                        |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| productCode                       | url attribute  | Id of product for which the variants should be displayed                                                    |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| code                              | request        | **(unique)** Product variant identifier                                                                     |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
|translations['localeCode']['name'] | request        | *(optional)* Name of the product variant                                                                    |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| position                          | request        | *(optional)* Position of variant in product                                                                 |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| tracked                           | request        | *(optional)* The information if the variant is tracked by inventory (true or false)                         |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| channelPricings                   | request        | *(optional)* Collection of objects which contains prices for all enabled channels                           |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| taxCategory                       | request        | *(optional)* Code of object which provides information about tax category to which variant is assigned      |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| shippingCategory                  | request        | *(optional)* Code of object which provides information about shipping category to which variant is assigned |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| optionValues                      | request        | *(optional)* Object with information about ProductOption (by code) and ProductOptionValue (by code)         |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| onHand                            | request        | *(optional)* Information about the number of product in given variant currently available in shop           |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| width                             | request        | *(optional)* The width of variant                                                                           |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| height                            | request        | *(optional)* The height of variant                                                                          |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| depth                             | request        | *(optional)* The depth of variant                                                                           |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+
| weight                            | request        | *(optional)* The weight of variant                                                                          |
+-----------------------------------+----------------+-------------------------------------------------------------------------------------------------------------+

.. warning::

    Channels must be created and enabled before the prices will be defined for they.

Example
^^^^^^^

Here is an example of creating a product variant with additional data for the product with ``code = MUG-TH``.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
             {
                "code": "double-theme-mug",
                "translations": {
                        "en_US": {
                            "name": "Double Theme Mug"
                    }
                },
                "channelPricings": [
                    {
                        "price": "1243"
                    }
                ],
                "tracked": true,
                "onHand": 5,
                "taxCategory": "other",
                "shippingCategory": "default",
                "optionValues": {
                    "mug_type": "mug_type_double"
                },
                "width": 5,
                "height": 10,
                "depth": 15,
                "weight": 20
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 335,
        "code": "double-theme-mug",
        "optionValues": [
            {
                "name": "Mug type",
                "code": "mug_type_double"
            }
        ],
        "position": 1,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 335,
                "name": "Double Theme Mug"
            }
        },
        "onHold": 0,
        "onHand": 5,
        "tracked": true,
        "weight": 20,
        "width": 5,
        "height": 10,
        "depth": 15,
        "taxCategory": {
            "id": 3,
            "code": "other",
            "name": "Other",
            "description": "Deserunt iure aspernatur et odit perferendis."
            "created_at": "2017-02-17T15:01:15+0100",
            "updated_at": "2017-02-17T15:01:15+0100",
            "_links": {
                "self": {
                    "href": "\/api\/v1\/tax-categories\/other"
                }
            }
        },
        "shippingCategory": {
            "id": 1,
            "code": "default",
            "name": "Default",
            "created_at": "2017-02-20T11:17:00+0100",
            "updated_at": "2017-02-20T11:17:01+0100",
            "_links": {
                "self": {
                    "href": "\/api\/v1\/shipping-categories\/default"
                }
            }
        },
        "channelPricings": [
            {
                "id": 333,
                "channel": {
                    "id": 1,
                    "code": "US_WEB",
                    "name": "US Web Store",
                    "hostname": "localhost",
                    "color": "Khaki",
                    "created_at": "2017-02-17T15:01:14+0100",
                    "updated_at": "2017-02-17T15:01:14+0100",
                    "enabled": true,
                    "tax_calculation_strategy": "order_items_based",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/channels\/US_WEB"
                        }
                    }
                },
                "price": 124300
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/MUG-TH\/variants\/double-theme-mug"
            },
            "product": {
                "href": "\/api\/v1\/products\/MUG-TH"
            }
        }
    }

Getting a Single Product Variant
--------------------------------

To retrieve the details of a product variant you will need to call the ``/api/v1/products/productCode/variants/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{productCode}/variants/{code}

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| code          | url attribute  | Identifier of the product variant                        |
+---------------+----------------+----------------------------------------------------------+
| productCode   | url attribute  | Id of product for which the variants should be displayed |
+---------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To see the details of the the product variant with ``code = medium-theme-mug``, which is defined for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/medium-theme-mug \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 333,
        "code": "medium-theme-mug",
        "optionValues": [
            {
                "name": "Mug type",
                "code": "mug_type_medium"
            }
        ],
        "position": 0,
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 333,
                "name": "Medium theme mug"
            }
        },
        "onHold": 0,
        "onHand": 0,
        "tracked": false,
        "channelPricings": [
            {
                "id": 331,
                "channel": {
                    "id": 1,
                    "code": "US_WEB",
                    "name": "US Web Store",
                    "hostname": "localhost",
                    "color": "Khaki",
                    "created_at": "2017-02-17T15:01:14+0100",
                    "updated_at": "2017-02-17T15:01:14+0100",
                    "enabled": true,
                    "tax_calculation_strategy": "order_items_based",
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/channels\/US_WEB"
                        }
                    }
                },
                "price": 1200
            }
        ],
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/MUG-TH\/variants\/medium-theme-mug"
            },
            "product": {
                "href": "\/api\/v1\/products\/MUG-TH"
            }
        }
    }

Collection of Product Variants
------------------------------

To retrieve a paginated list of variants for a selected product you will need to call the ``/api/v1/products/productCode/variants/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{productCode}/variants/

+---------------------------------------+----------------+------------------------------------------------------------+
| Parameter                             | Parameter type | Description                                                |
+=======================================+================+============================================================+
| Authorization                         | header         | Token received during authentication                       |
+---------------------------------------+----------------+------------------------------------------------------------+
| productCode                           | url attribute  | Code of product for which the variants should be displayed |
+---------------------------------------+----------------+------------------------------------------------------------+
| limit                                 | query          | *(optional)* Number of items to display per page,          |
|                                       |                | by default = 10                                            |
+---------------------------------------+----------------+------------------------------------------------------------+
| sorting['name_of_field']['direction'] | query          | *(optional)* Field and direction of sorting,               |
|                                       |                | by default 'desc' and 'createdAt'                          |
+---------------------------------------+----------------+------------------------------------------------------------+

Example
^^^^^^^

To see the first page of all product variants for the product with ``code = MUG-TH`` use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "page": 1,
        "limit": 10,
        "pages": 1,
        "total": 1,
        "_links": {
            "self": {
                "href": "\/api\/v1\/products\/MUG-TH\/variants\/?page=1&limit=10"
            },
            "first": {
                "href": "\/api\/v1\/products\/MUG-TH\/variants\/?page=1&limit=10"
            },
            "last": {
                "href": "\/api\/v1\/products\/MUG-TH\/variants\/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 333,
                    "code": "medium-theme-mug",
                    "optionValues": [
                        {
                            "name": "Mug type",
                            "code": "mug_type_medium"
                        }
                    ],
                    "position": 0,
                    "translations": {
                        "en_US": {
                            "locale": "en_US",
                            "id": 333,
                            "name": "Medium theme mug"
                        }
                    },
                    "tracked": false,
                    "channelPricings": [
                        {
                            "id": 331,
                            "channel": {
                                "id": 1,
                                "code": "US_WEB",
                                "_links": {
                                    "self": {
                                        "href": "\/api\/v1\/channels\/US_WEB"
                                    }
                                }
                            },
                            "price": 1200
                        }
                    ],
                    "_links": {
                        "self": {
                            "href": "\/api\/v1\/products\/MUG-TH\/variants\/medium-theme-mug"
                        },
                        "product": {
                            "href": "\/api\/v1\/products\/MUG-TH"
                        }
                    }
                }
            ]
        }
    }

Updating Product Variant
------------------------

To fully update a product variant you will need to call the ``/api/v1/products/productCode/variants/code`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/products/{productCode}/variants/{code}

+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| Parameter                          | Parameter type | Description                                                                                                     |
+====================================+================+=================================================================================================================+
| Authorization                      | header         | Token received during authentication                                                                            |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| code                               | url attribute  | Identifier of the product variant                                                                               |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| productCode                        | url attribute  | Id of product for which the variants should be displayed                                                        |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| translations['localeCode']['name'] | request        | *(optional)* Name of the product variant                                                                        |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| position                           | request        | *(optional)* Position of the variant in product                                                                 |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| tracked                            | request        | *(optional)* The information if the variant is tracked by inventory (true or false)                             |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| channelPricings                    | request        | *(optional)* Collection of prices for all the enabled channels                                                  |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| taxCategory                        | request        | *(optional)* Code of object which provides information about tax category to which the variant is assigned      |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| shippingCategory                   | request        | *(optional)* Code of object which provides information about shipping category to which the variant is assigned |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| optionValues                       | request        | *(optional)* Object with information about ProductOption (by code) and ProductOptionValue (by code)             |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| onHand                             | request        | *(optional)* Information about the number of product in the given variant currently available in shop           |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| width                              | request        | *(optional)* The width of the variant                                                                           |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| height                             | request        | *(optional)* The height of the variant                                                                          |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| depth                              | request        | *(optional)* The depth of the variant                                                                           |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+
| weight                             | request        | *(optional)* The weight of the variant                                                                          |
+------------------------------------+----------------+-----------------------------------------------------------------------------------------------------------------+

Example
^^^^^^^

To fully update the product variant with ``code = double-theme-mug`` for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/double-theme-mug \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
                "translations":{
                    "en_US": {
                        "name": "Monster mug"
                    }
                },
                "channelPricings": [
                    {
                        "price": "54"
                    }
                ],
                "tracked": true,
                "onHand": 3,
                "taxCategory": "other",
                "shippingCategory": "default",
                "width": 5,
                "height": 10,
                "depth": 15,
                "weight": 20,
                "optionValues": {
                    "mug_type" :"mug_type_monster"
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

To partially update a product variant you will need to call the ``/api/v1/products/productCode/variants/code`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/products/{productCode}/variants/{code}

+------------------------------------+----------------+----------------------------------------------------------+
| Parameter                          | Parameter type | Description                                              |
+====================================+================+==========================================================+
| Authorization                      | header         | Token received during authentication                     |
+------------------------------------+----------------+----------------------------------------------------------+
| code                               | url attribute  | Identifier of the product variant                        |
+------------------------------------+----------------+----------------------------------------------------------+
| productCode                        | url attribute  | Id of product for which the variants should be displayed |
+------------------------------------+----------------+----------------------------------------------------------+
| translations['localeCode']['name'] | request        | Name of product variant                                  |
+------------------------------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To partially update the product variant with ``code = double-theme-mug`` for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/double-theme-mug \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "translations": {
                    "de": {
                        "name": "Monsterbecher"
                    }
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Product Variant
--------------------------

To delete a product variant you will need to call the ``/api/v1/products/productCode/variants/code`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/products/{productCode}/variants/{code}

+-------------------------------------+----------------+----------------------------------------------------------+
| Parameter                           | Parameter type | Description                                              |
+=====================================+================+==========================================================+
| Authorization                       | header         | Token received during authentication                     |
+-------------------------------------+----------------+----------------------------------------------------------+
| code                                | url attribute  | Identifier of the product variant                        |
+-------------------------------------+----------------+----------------------------------------------------------+
| productCode                         | url attribute  | Id of product for which the variants should be displayed |
+-------------------------------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To delete the product variant with ``code = double-theme-mug`` from the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/variants/double-theme-mug \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
