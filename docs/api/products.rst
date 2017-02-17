Products API
============

These endpoints will allow you to easily manage products. Base URI is `/api/v1/products`.

Product API response structure
------------------------------

If you request a product via API, you will receive an object with the following fields:

+----------------+----------------------------------------------------------------------------+
| Field          | Description                                                                |
+================+============================================================================+
| id             | Id of the product                                                          |
+----------------+----------------------------------------------------------------------------+
| code           | Unique product identifier (for example SKU)                                |
+----------------+----------------------------------------------------------------------------+
| average_rating | Average from accepted ratings given by customer                            |
+----------------+----------------------------------------------------------------------------+
| channels       | Collection of channels to which the product was assigned                   |
+----------------+----------------------------------------------------------------------------+
| translations   | Collection of translations (each contains slug and name in given language) |
+----------------+----------------------------------------------------------------------------+
| options        | Options assigned to the product                                            |
+----------------+----------------------------------------------------------------------------+
| images         | Images assigned to the product                                             |
+----------------+----------------------------------------------------------------------------+

If you request for more detailed data, you will receive an object with the following fields:

+-----------------+----------------------------------------------------------------------------+
| Field           | Description                                                                |
+=================+============================================================================+
| id              | Id of the product                                                          |
+-----------------+----------------------------------------------------------------------------+
| code            | Unique product identifier                                                  |
+-----------------+----------------------------------------------------------------------------+
| average_rating  | Average from ratings given by customer                                     |
+-----------------+----------------------------------------------------------------------------+
| channels        | Collection of channels to which the product was assigned                   |
+-----------------+----------------------------------------------------------------------------+
| translations    | Collection of translations (each contains slug and name in given language) |
+-----------------+----------------------------------------------------------------------------+
| attributes      | Collection of attributes connected with the product (for example material) |
+-----------------+----------------------------------------------------------------------------+
| associations    | Collection of products associated with the created product                 |
|                 | (for example accessories to this product)                                  |
+-----------------+----------------------------------------------------------------------------+
| variants        | Collection of variants connected with the product                          |
+-----------------+----------------------------------------------------------------------------+
| reviews         | Collection of reviews passed by customers                                  |
+-----------------+----------------------------------------------------------------------------+
| product_taxons  | Collection of relations between product and taxons                         |
+-----------------+----------------------------------------------------------------------------+
| main_taxon      | The main taxon to whose the product is assigned                            |
+-----------------+----------------------------------------------------------------------------+


.. note::

	Read more about :doc:`Product model in the component docs</components/Product/models>`.

Creating a Product
------------------

To create a new product you will need to call the ``/api/v1/products/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

	POST /api/v1/products/

+------------------------------------+----------------+--------------------------------------+
| Parameter                          | Parameter type | Description                          |
+====================================+================+======================================+
| Authorization                      | header         | Token received during authentication |
+------------------------------------+----------------+--------------------------------------+
| code                               | request        | **(unique)** Product identifier      |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['name'] | request        | Name of the product                  |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['slug'] | request        | **(unique)** Slug                    |
+------------------------------------+----------------+--------------------------------------+

Example
^^^^^^^

To create a new product use the below method:

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/ \
		-H "Authorization: Bearer SampleToken" \
		-H "Content-Type: application/json" \
		-X POST \
		--data '
			{
				"code": "TS3"
			}
		'

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

	STATUS: 201 CREATED

.. code-block:: json

	{
		"name": "Truck Simulator",
		"id": 61,
		"code": "TS3",
		"attributes": [],
		"variants": [],
		"options": [],
		"associations": [],
		"translations": [],
		"product_taxons": [],
		"reviews": [],
		"average_rating": 0,
		"images": [],
		"_links": {
			"self": {
				"href": "\/api\/v1\/products\/TS3"
			}
		}
	}
.. warning::

	If you try to create a product without name, code or slug, you will receive a ``400 Bad Request`` error, that will contain validation errors.

Example
^^^^^^^

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/ \
		-H "Authorization: Bearer SampleToken" \
		-H "Accept: application/json" \
		-X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

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

You can also create a product with additional (not required) fields:

+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| Parameter                           | Parameter type | Description                                                                       |
+=====================================+================+===================================================================================+
| channels                            | request        | Collection of channels codes, which we want to associate with created product     |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| translations['locale_code']['name'] | request        | Collection of translations (each contains slug and name in given locale).         |
| translations['locale_code']['slug'] |                | Only the translation for default locale is required, the rest are optional        |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| options                             | request        | Collection of options codes, which we want to associate with created product      |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| images                              | request        | Collection of images codes, which we want to associate with created product       |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| attributes                          | request        | Array of attributes (each object has information about selected attribute's code, |
|                                     |                | its value and locale in which it was defined)                                     |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| associations                        | request        | Object with code of productAssociationType and string in which the codes of       |
|                                     |                | associated products was written down.                                             |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| product_taxons                      | request        | String in which the codes of taxons was written down (separated by comma)         |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+
| main_taxon                          | request        | The main taxon's code to whose product is assigned                                |
+-------------------------------------+----------------+-----------------------------------------------------------------------------------+

Example
^^^^^^^

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/ \
		-H "Authorization: Bearer SampleToken" \
		-H "Accept: application/json" \
		-X POST \
		--data '
			{
				"code": "MUG_TH",
				"main_taxon": "mugs",
				"product_taxons": "mugs",
				"channels": [
					"US_WEB"
				],
				"attributes": [
					 {
						 "attribute": "mug_material",
						 "locale_code": "en_US",
						 "value": "concrete"
					 }
				 ],
				"options": [
					"mug_type"
				],
				 "associations": {
					 "accessories": "f1fd2fab,f1fd2fab-c024"
				 },
				"translations": {
					"en_US": {
						"name": "Theme Mug",
						"slug": "theme-mug"
					},
					"pl_PL": {
						"name": "Kubek z motywem",
						"slug": "kubek-z-motywem"
					}
				},
				"images": [
					{
						"type": "ford"
					}
				]
			}
		'

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

	STATUS: 201 CREATED

.. code-block:: json

	{
		"name": "Theme Mug",
		"id": 65,
		"code": "MUG_TH",
		"attributes": [
			{
				"code": "mug_material",
				"name": "Mug material",
				"value": "concrete",
				"type": "text",
				"id": 137
			}
		],
		"variants": [],
		"options": [
			{
				"id": 1,
				"code": "mug_type",
				"position": 0,
				"values": [
					{
						"name": "Mug type",
						"code": "mug_type_medium"
					},
					{
						"name": "Mug type",
						"code": "mug_type_double"
					},
					{
						"name": "Mug type",
						"code": "mug_type_monster"
					}
				]
			}
		],
		"associations": [
			{
				"id": 12,
				"type": {
					"id": 2,
					"code": "accessories",
					"created_at": "2017-02-20T09:06:21+0100",
					"updated_at": "2017-02-20T09:06:21+0100",
					"translations": [
						{
							"locale": "en_US",
							"id": 2,
							"name": "Accessories"
						}
					],
					"current_locale": "en_US",
					"fallback_locale": "en_US"
				},
				"associated_products": [
					{
						"name": "Spoon",
						"id": 62,
						"code": "spoon",
						"attributes": [],
						"variants": [
							{
								"id": 331,
								"code": "spoon",
								"option_values": [],
								"position": 0,
								"translations": {
									"en_US": {
										"locale": "en_US",
										"id": 331
									}
								},
								"on_hold": 0,
								"on_hand": 0,
								"tracked": false,
								"channel_pricings": [],
								"_links": {
									"self": {
										"href": "\/api\/v1\/products\/spoon\/variants\/spoon"
									},
									"product": {
										"href": "\/api\/v1\/products\/spoon"
									}
								}
							}
						],
						"options": [],
						"associations": [],
						"translations": {
							"en_US": {
								"locale": "en_US",
								"id": 62,
								"name": "Spoon",
								"slug": "spoon"
							}
						},
						"product_taxons": [],
						"reviews": [],
						"average_rating": 0,
						"images": [],
						"_links": {
							"self": {
								"href": "\/api\/v1\/products\/spoon"
							}
						}
					},
					{
						"name": "Coffee",
						"id": 63,
						"code": "coffee",
						"attributes": [],
						"variants": [
							{
								"id": 332,
								"code": "coffee",
								"option_values": [],
								"position": 0,
								"translations": {
									"en_US": {
										"locale": "en_US",
										"id": 332
									}
								},
								"on_hold": 0,
								"on_hand": 0,
								"tracked": false,
								"channel_pricings": [],
								"_links": {
									"self": {
										"href": "\/api\/v1\/products\/coffee\/variants\/coffee"
									},
									"product": {
										"href": "\/api\/v1\/products\/coffee"
									}
								}
							}
						],
						"options": [],
						"associations": [],
						"translations": {
							"en_US": {
								"locale": "en_US",
								"id": 63,
								"name": "Coffee",
								"slug": "coffee"
							}
						},
						"product_taxons": [],
						"reviews": [],
						"average_rating": 0,
						"images": [],
						"_links": {
							"self": {
								"href": "\/api\/v1\/products\/coffee"
							}
						}
					}
				],
				"created_at": "2017-02-20T09:23:28+0100",
				"updated_at": "2017-02-20T09:23:28+0100"
			}
		],
		"translations": {
			"en_US": {
				"locale": "en_US",
				"id": 66,
				"name": "Theme Mug",
				"slug": "theme-mug"
			},
			"pl": {
				"locale": "pl",
				"id": 67,
				"name": "Kubek z motywem",
				"slug": "kubek-z-motywem"
			}
		},
		"product_taxons": [
			{
				"id": 77,
				"taxon": {
					"name": "Mugs",
					"id": 2,
					"code": "mugs",
					"root": {
						"name": "Category",
						"id": 1,
						"code": "category",
						"children": [],
						"left": 1,
						"right": 12,
						"level": 0,
						"position": 0,
						"translations": {
							"en_US": {
								"locale": "en_US",
								"id": 1,
								"name": "Category",
								"slug": "category",
								"description": "Cum explicabo deserunt temporibus beatae et est quis."
							}
						},
						"images": [],
						"_links": {
							"self": {
								"href": "\/api\/v1\/taxons\/category"
							}
						}
					},
					"parent": {
						"name": "Category",
						"id": 1,
						"code": "category",
						"children": [],
						"left": 1,
						"right": 12,
						"level": 0,
						"position": 0,
						"translations": {
							"en_US": {
								"locale": "en_US",
								"id": 1,
								"name": "Category",
								"slug": "category",
								"description": "Cum explicabo deserunt temporibus beatae et est quis."
							}
						},
						"images": [],
						"_links": {
							"self": {
								"href": "\/api\/v1\/taxons\/category"
							}
						}
					},
					"children": [],
					"left": 2,
					"right": 3,
					"level": 1,
					"position": 0,
					"translations": {
						"en_US": {
							"locale": "en_US",
							"id": 2,
							"name": "Mugs",
							"slug": "mugs",
							"description": "Placeat dolor aut dolorum minima."
						}
					},
					"images": [],
					"_links": {
						"self": {
							"href": "\/api\/v1\/taxons\/mugs"
						}
					}
				},
				"position": 0
			}
		],
		"main_taxon": {
			"name": "Mugs",
			"id": 2,
			"code": "mugs",
			"root": {
				"name": "Category",
				"id": 1,
				"code": "category",
				"children": [],
				"left": 1,
				"right": 12,
				"level": 0,
				"position": 0,
				"translations": {
					"en_US": {
						"locale": "en_US",
						"id": 1,
						"name": "Category",
						"slug": "category",
						"description": "Cum explicabo deserunt temporibus beatae et est quis."
					}
				},
				"images": [],
				"_links": {
					"self": {
						"href": "\/api\/v1\/taxons\/category"
					}
				}
			},
			"parent": {
				"name": "Category",
				"id": 1,
				"code": "category",
				"children": [],
				"left": 1,
				"right": 12,
				"level": 0,
				"position": 0,
				"translations": {
					"en_US": {
						"locale": "en_US",
						"id": 1,
						"name": "Category",
						"slug": "category",
						"description": "Cum explicabo deserunt temporibus beatae et est quis."
					}
				},
				"images": [],
				"_links": {
					"self": {
						"href": "\/api\/v1\/taxons\/category"
					}
				}
			},
			"children": [],
			"left": 2,
			"right": 3,
			"level": 1,
			"position": 0,
			"translations": {
				"en_US": {
					"locale": "en_US",
					"id": 2,
					"name": "Mugs",
					"slug": "mugs",
					"description": "Placeat dolor aut dolorum minima."
				}
			},
			"images": [
				{
					"id": 1,
					"type": "ford",
					"path": "b9/65/01cec3d87aa2b819e195331843f6.jpeg"
				}
			],
			"_links": {
				"self": {
					"href": "\/api\/v1\/taxons\/mugs"
				}
			}
		},
		"reviews": [],
		"average_rating": 0,
		"images": [],
		"_links": {
			"self": {
				"href": "\/api\/v1\/products\/MUG_TH"
			}
		}
	}
}

.. note::

	The images (files) should be passed in an array as an attribute of request. See how it is done in Sylius
	`here <https://github.com/Sylius/Sylius/blob/master/tests/Controller/ProductApiTest.php>`_.

Getting a Single Product
------------------------

To retrieve the details of the product you will need to call the ``/api/v1/product/code`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

	GET /api/v1/products/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product identifier            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To see the details for the the product with ``code = spoon`` use the below method:

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/spoon \
		-H "Authorization: Bearer SampleToken" \
		-H "Accept: application/json"

.. note::

	*spoon* is an exemplary value. Your value can be different.
	Check in the list of all products if you are not sure which code should be used.

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

	STATUS: 200 OK

.. code-block:: json

	{
		"name": "Spoon",
		"id": 62,
		"code": "spoon",
		"attributes": [],
		"variants": [
			{
				"id": 331,
				"code": "spoon",
				"option_values": [],
				"position": 0,
				"translations": {
					"en_US": {
						"locale": "en_US",
						"id": 331
					}
				},
				"on_hold": 0,
				"on_hand": 0,
				"tracked": false,
				"channel_pricings": [],
				"_links": {
					"self": {
						"href": "\/api\/v1\/products\/spoon\/variants\/spoon"
					},
					"product": {
						"href": "\/api\/v1\/products\/spoon"
					}
				}
			}
		],
		"options": [],
		"associations": [],
		"translations": {
			"en_US": {
				"locale": "en_US",
				"id": 62,
				"name": "Spoon",
				"slug": "spoon"
			}
		},
		"product_taxons": [],
		"reviews": [],
		"average_rating": 0,
		"images": [],
		"_links": {
			"self": {
				"href": "\/api\/v1\/products\/spoon"
			}
		}
	}

Collection of Products
----------------------

To retrieve a paginated list of products you will need to call the ``/api/v1/products/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

	GET /api/v1/products/

+---------------------------------------+----------------+---------------------------------------------------+
| Parameter                             | Parameter type | Description                                       |
+=======================================+================+===================================================+
| Authorization                         | header         | Token received during authentication              |
+---------------------------------------+----------------+---------------------------------------------------+
| limit                                 | query          | *(optional)* Number of items to display per page, |
|                                       |                | by default = 10                                   |
+---------------------------------------+----------------+---------------------------------------------------+
| sorting['name_of_field']['direction'] | query          | *(optional)* Field and direction of sorting,      |
|                                       |                | by default 'desc' and 'createdAt'                 |
+---------------------------------------+----------------+---------------------------------------------------+

To see the first page of all products use the below method:

Example
^^^^^^^

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/ \
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
		"total": 4,
		"_links": {
			"self": {
				"href": "\/api\/v1\/products\/?page=1&limit=10"
			},
			"first": {
				"href": "\/api\/v1\/products\/?page=1&limit=10"
			},
			"last": {
				"href": "\/api\/v1\/products\/?page=1&limit=10"
			}
		},
		"_embedded": {
			"items": [
				{
					"name": "Coffee",
					"id": 63,
					"code": "coffee",
					"options": [],
					"average_rating": 0,
					"images": [],
					"_links": {
						"self": {
							"href": "\/api\/v1\/products\/coffee"
						}
					}
				},
				{
					"name": "Theme Mug",
					"id": 65,
					"code": "MUG_TH",
					"options": [
						{
							"id": 1,
							"code": "mug_type",
							"position": 0,
							"values": [
								{
									"name": "Mug type",
									"code": "mug_type_medium"
								},
								{
									"name": "Mug type",
									"code": "mug_type_double"
								},
								{
									"name": "Mug type",
									"code": "mug_type_monster"
								}
							]
						}
					],
					"average_rating": 0,
					"images": [],
					"_links": {
						"self": {
							"href": "\/api\/v1\/products\/MUG_TH"
						}
					}
				},
				{
					"name": "Spoon",
					"id": 62,
					"code": "spoon",
					"options": [],
					"average_rating": 0,
					"images": [],
					"_links": {
						"self": {
							"href": "\/api\/v1\/products\/spoon"
						}
					}
				},
				{
					"name": "Truck Simulator",
					"id": 61,
					"code": "TS3",
					"options": [],
					"average_rating": 0,
					"images": [],
					"_links": {
						"self": {
							"href": "\/api\/v1\/products\/TS3"
						}
					}
				}
			]
		}
	}

Updating a Product
------------------

To fully update a product you will need to call the ``/api/v1/products/code`` endpoint with ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

	PUT /api/v1/products/{code}

+------------------------------------+----------------+--------------------------------------+
| Parameter                          | Parameter type | Description                          |
+====================================+================+======================================+
| Authorization                      | header         | Token received during authentication |
+------------------------------------+----------------+--------------------------------------+
| code                               | url attribute  | Unique product identifier            |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['name'] | request        | Name of the product                  |
+------------------------------------+----------------+--------------------------------------+
|translations['locale_code']['slug'] | request        | **(unique)** Slug                    |
+------------------------------------+----------------+--------------------------------------+

Example
^^^^^^^

 To fully update the product with ``code = spoon`` use the below method:

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/spoon \
		-H "Authorization: Bearer SampleToken" \
		-H "Content-Type: application/json" \
		-X PUT \
		--data '
			{
				"translations": {
					"en_US": {
						"name": "Small spoon",
						"slug": "small-spoon"
					}
				}
			}
		'

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

If you try to perform a full product update without all required fields specified, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/spoon \
		-H "Authorization: Bearer SampleToken" \
		-H "Accept: application/json" \
		-X PUT

Exemplary Response
^^^^^^^^^^^^^^^^^^

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

To update a product partially you will need to call the ``/api/v1/products/code`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

	PATCH /api/v1/products/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product identifier            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To partially update the product with ``code = spoon`` use the below method:

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/spoon \
		-H "Authorization: Bearer SampleToken" \
		-H "Content-Type: application/json" \
		-X PATCH \
		--data '
			{
				"translations": {
					"en__US": {
						"name": "Small spoon"
					}
				}
			}
		'

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Product
------------------

To delete a product you will need to call the ``/api/v1/products/code`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

	DELETE /api/v1/products/{code}

+---------------+----------------+--------------------------------------+
| Parameter     | Parameter type | Description                          |
+===============+================+======================================+
| Authorization | header         | Token received during authentication |
+---------------+----------------+--------------------------------------+
| code          | url attribute  | Unique product identifier            |
+---------------+----------------+--------------------------------------+

Example
^^^^^^^

To delete the product with ``code = spoon`` use the below method:

.. code-block:: bash

	$ curl http://demo.sylius.org/api/v1/products/spoon \
		-H "Authorization: Bearer SampleToken" \
		-H "Accept: application/json" \
		-X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
