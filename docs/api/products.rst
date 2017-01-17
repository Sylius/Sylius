Products API
============

These endpoints will allow you to easily manage products. Base URI is `/api/v1/products`.

When you get a collection of resources, "Default" serialization group will be used and following fields will be exposed:

+----------------+----------------------------------------------------------------------------+
| Field          | Description                                                                |
+================+============================================================================+
| id             | Id of product                                                              |
+----------------+----------------------------------------------------------------------------+
| code           | Unique product identifier                                                  |
+----------------+----------------------------------------------------------------------------+
| average_rating | Average from ratings given by customer                                     |
+----------------+----------------------------------------------------------------------------+
| channels       | Collection of channels to which product was assigned                       |
+----------------+----------------------------------------------------------------------------+
| translations   | Collection of translations (each contains slug and name in given language) |
+----------------+----------------------------------------------------------------------------+

If you request for a more detailed data, you will receive an object with following fields:

+----------------+----------------------------------------------------------------------------+
| Field          | Description                                                                |
+================+============================================================================+
| id             | Id of product                                                              |
+----------------+----------------------------------------------------------------------------+
| code           | Unique product identifier                                                  |
+----------------+----------------------------------------------------------------------------+
| average_rating | Average from ratings given by customer                                     |
+----------------+----------------------------------------------------------------------------+
| channels       | Collection of channels to which product was assigned                       |
+----------------+----------------------------------------------------------------------------+
| translations   | Collection of translations (each contains slug and name in given language) |
+----------------+----------------------------------------------------------------------------+
| attributes     | Collection of attributes connected with product (for example material)     |
+----------------+----------------------------------------------------------------------------+
| variants       | Collection of variants connected with product                              |
+----------------+----------------------------------------------------------------------------+

.. note::

    Read more about `Products`__

__ http://docs.sylius.org/en/latest/components/Product/models.html#product

Collection of Products
----------------------

You can retrieve the full products list by making the following request:

Definition
..........

.. code-block:: text

    GET /api/v1/products/

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+


Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        {
	"page": 1,
	"limit": 10,
	"pages": 1,
	"total": 1,
	"_links": {
		"self": {
			"href": "/api/v1/products/?page=1&limit=10"
		},
		"first": {
			"href": "/api/v1/products/?page=1&limit=10"
		},
		"last": {
			"href": "/api/v1/products/?page=7&limit=10"
		},
		"next": {
			"href": "/api/v1/products/?page=2&limit=10"
		}
	},
	"_embedded": {
		"items": [
			{
				"id": 61,
				"code": "Banana",
				"translations": {
					"en_US": {
                        "id": 61,
						"locale": "en_US",
						"name": "Banana",
						"slug": "banana"
					}
				},
				"_links": {
					"self": {
						"href": "/api/v1/products/61"
					}
				}
			},
        ]
    }

Getting a Single Product
------------------------

You can request detailed product information by executing the following request:

Definition
..........

.. code-block:: text

    GET /api/v1/products/{id}

+---------------+----------------+-------------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                       |
+===============+================+===================================================================+
| Authorization | header         | Token received during authentication                              |
+---------------+----------------+-------------------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                          |
+---------------+----------------+-------------------------------------------------------------------+
| page          | query          | *(optional)* Number of the page, by default = 1                   |
+---------------+----------------+-------------------------------------------------------------------+
| limit         | query          | *(optional)* Number of items to display per page, by default = 10 |
+---------------+----------------+-------------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/52
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
	"id": 52,
	"code": "b32c7f49-0693-3f3a-a57b-e86b4eb00e96",
	"attributes": [
		{
			"code": "t_shirt_material",
			"name": "T-Shirt material",
			"value": "Potato 100%",
			"id": 111
		}
	],
	"variants": [
		{
			"id": 196,
			"on_hold": 0,
			"tracked": false
		},
		{
			"id": 197,
			"on_hold": 0,
			"tracked": false
		},
		{
			"id": 198,
			"on_hold": 0,
			"tracked": false
		}
	],
	"translations": {
		"en_US": {
			"locale": "en_US",
			"id": 52,
			"name": "T-Shirt \"voluptate\"",
			"slug": "t-shirt-voluptate",
			"description": "Libero nihil odit exercitationem repellendus consequuntur libero aut.",
			"short_description": "Expedita laudantium ea quod molestias totam."
		}
	},
	"_links": {
		"self": {
			"href": "/api/v1/products/52"
		}
	}
}

Creating Product
----------------

Definition
..........

.. code-block:: text

    POST /api/v1/products/

+---------------+----------------+--------------------------------------------------------+
| Parameter     | Parameter type | Description                                            |
+===============+================+========================================================+
| Authorization | header         | Token received during authentication                   |
+---------------+----------------+--------------------------------------------------------+
| name          | request        | Name of creating product                               |
+---------------+----------------+--------------------------------------------------------+
| code          | request        | **(unique)** Product identifier                        |
+---------------+----------------+--------------------------------------------------------+
| slug          | request        | **(unique)**  Name converted to valid format for URL   |
+---------------+----------------+--------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X POST
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "Truck Simulator",
                        "slug": "truck-simulator"
                    }
                },
                "code": "TS3"
            }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 201 CREATED

.. code-block:: json

    {
        "id": 64,
        "code": "TS3",
        "attributes": [],
        "variants": [],
        "translations": {
            "en_US": {
                "locale": "en_US",
                "id": 64,
                "name": "Truck Simulator",
                "slug": "truck-simulator"
            }
        },
        "_links": {
            "self": {
                "href": "/api/v1/products/64"
            }
        }
    }

If you try to create a resource without name, code or slug, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X POST

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "enabled": {},
                "translations": {
                    "children": {
                        "en_US": {
                            "children": {
                                "name": {
                                    "errors": [
                                        "Please enter product name."
                                    ]
                                },
                                "slug": {
                                    "errors": [
                                        "Please enter product slug."
                                    ]
                                },
                                "description": {},
                                "metaKeywords": {},
                                "metaDescription": {},
                                "shortDescription": {}
                            }
                        }
                    }
                },
                "attributes": {},
                "associations": {
                    "children": {
                        "similar_products": {}
                    }
                },
                "channels": {
                    "children": [
                        {}
                    ]
                },
                "mainTaxon": {},
                "productTaxons": {},
                "images": {},
                "code": {
                    "errors": [
                        "Please enter product code."
                    ]
                },
                "options": {}
            }
        }
    }


Updating Product
----------------

You can request full or partial update of resource. For full product update, you should use PUT method.

Definition
..........

.. code-block:: text

    PUT /api/v1/products/{id}

+---------------+----------------+---------------------------------------------------+
| Parameter     | Parameter type | Description                                       |
+===============+================+===================================================+
| Authorization | header         | Token received during authentication              |
+---------------+----------------+---------------------------------------------------+
| id            | url attribute  | Id of requested resource                          |
+---------------+----------------+---------------------------------------------------+
| name          | request        | Name of updating product                          |
+---------------+----------------+---------------------------------------------------+
| slug          | request        | Name of product converted to valid format for URL |
+---------------+----------------+---------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/product/3
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X PUT
        --data '
            {
                "translations": {
                    "en_US": {
                        "name": "nice banana",
                        "slug": "nice-banana"
                }    }
	        }
        '

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

If you try to perform full product update without all required fields specified, you will receive a 400 error.

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/3
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X PUT

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 400 Bad Request

.. code-block:: json

    {
        "code": 400,
        "message": "Validation Failed",
        "errors": {
            "children": {
                "enabled": {},
                "translations": {
                    "children": {
                        "en_US": {
                            "children": {
                                "name": {
                                    "errors": [
                                        "Please enter product name."
                                    ]
                                },
                                "slug": {
                                    "errors": [
                                        "Please enter product slug."
                                    ]
                                },
                                "description": {},
                                "metaKeywords": {},
                                "metaDescription": {},
                                "shortDescription": {}
                            }
                        }
                    }
                },
                "attributes": {},
                "associations": {
                    "children": {
                        "similar_products": {}
                    }
                },
                "channels": {
                    "children": [
                        {}
                    ]
                },
                "mainTaxon": {},
                "productTaxons": {},
                "images": {},
                "code": {},
                "options": {}
            }
	    }
    }

In order to perform a partial update, you should use a PATCH method.

Definition
..........

.. code-block:: text

    PATCH /api/v1/products/{id}

+---------------+----------------+-----------------------------------------------------------------+
| Parameter     | Parameter type | Description                                                     |
+===============+================+=================================================================+
| Authorization | header         | Token received during authentication                            |
+---------------+----------------+-----------------------------------------------------------------+
| id            | url attribute  | Id of requested resource                                        |
+---------------+----------------+-----------------------------------------------------------------+
| name          | request        | *(optional)* Name of updating product                           |
+---------------+----------------+-----------------------------------------------------------------+
| slug          | request        | *(optional)*  Name of product converted to valid format for URL |
+---------------+----------------+-----------------------------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/product/3
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H "Content-Type: application/json"
        -X PATCH
        --data '{"name": "Nice Banana"}'

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content

Deleting Product
----------------

Definition
..........

.. code-block:: text

    DELETE /api/v1/products/{id}

+---------------+----------------+-------------------------------------------+
| Parameter     | Parameter type | Description                               |
+===============+================+===========================================+
| Authorization | header         | Token received during authentication      |
+---------------+----------------+-------------------------------------------+
| id            | url attribute  | Id of removed product                     |
+---------------+----------------+-------------------------------------------+

Example
.......

.. code-block:: bash

    curl http://sylius.dev/api/v1/products/3
        -H "Authorization: Bearer MWExMWM0NzE1NmUyZDgyZDJiMjEzMmFlMjQ4MzgwMmE4ZTkxYzM0YjdlN2U2YzliNDIyMTk1ZDhlNDYxYWE4Ng"
        -H “Accept: application/json”
        -X DELETE

Example Response
~~~~~~~~~~~~~~~~

.. code-block:: text

    STATUS: 204 No Content
