Product Reviews API
====================

These endpoints will allow you to easily manage product reviews. Base URI is `/api/v1/products/{productCode}/reviews/`.

Product Reviews API response structure
--------------------------------------

When you get a collection of resources, you will receive objects with the following fields:

+------------------+------------------------------------------------------------------------------------------------+
| Field            | Description                                                                                    |
+==================+================================================================================================+
| id               | Id of product review                                                                           |
+------------------+------------------------------------------------------------------------------------------------+
| title            | Title of product review                                                                        |
+------------------+------------------------------------------------------------------------------------------------+
| comment          | Comment of product review                                                                      |
+------------------+------------------------------------------------------------------------------------------------+
| author           | Customer author for product review (This is customer that added the                            |
|                  | product review; this will contain customer resource information)                               |
+------------------+------------------------------------------------------------------------------------------------+
| status           | Status of product review (New, Accepted, Rejected)                                             |
+------------------+------------------------------------------------------------------------------------------------+
| reviewSubject    | This is the review subject for the product review. For this case of the product review, this   |
|                  | will contain a product resource                                                                |
+------------------+------------------------------------------------------------------------------------------------+

.. note::

    Read more about :doc:`ProductReviews docs</book/products/product_reviews>`.

Creating a Product Review
--------------------------

To create a new product review you will need to call the ``/api/v1/products/{productCode}/reviews/`` endpoint with the ``POST`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/products/{productCode}/reviews/

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| productCode   | url attribute  | Code of product for which the reviews should be created  |
+---------------+----------------+----------------------------------------------------------+
| title         | request        | Product review title                                     |
+---------------+----------------+----------------------------------------------------------+
| comment       | request        | Product review comment                                   |
+---------------+----------------+----------------------------------------------------------+
| rating        | request        | Product review rating (1..5)                             |
+---------------+----------------+----------------------------------------------------------+
| author        | request        | Product review author                                    |
+---------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To create a new product review for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X POST \
        --data '
            {
                "title": "A product review",
                "rating": "3",
                "comment": "This is a comment review",
                "author": {
                    "email": "test@example.com"
                }
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 201 Created

.. code-block:: json

    {
        "id": 4,
        "title": "A product review",
        "rating": 3,
        "comment": "This is a comment review",
        "author": {
            "id": 2,
            "email": "test@example.com",
            "emailCanonical": "test@example.com",
            "gender": "u",
            "_links": {
                "self": {
                    "href": "/api/v1/customers/2"
                }
            }
        },
        "status": "new",
        "reviewSubject": {
            "id": 1,
            "name": "MUG-TH",
            "code": "MUG-TH",
            "attributes": [],
            "options": [],
            "associations": [],
            "translations": []
        }
    }

.. warning::

    If you try to create a resource without title, rating, comment or author, you will receive a ``400 Bad Request`` error.

Example
^^^^^^^

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/ \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
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
                "rating": {
                    "errors": [
                        "You must check review rating."
                    ],
                    "children": [
                        {},
                        {},
                        {},
                        {},
                        {}
                    ]
                },
                "title": {
                    "errors": [
                        "Review title should not be blank."
                    ]
                },
                "comment": {
                    "errors": [
                        "Review comment should not be blank."
                    ]
                },
                "author": {
                    "children": {
                        "email": {
                            "errors": [
                                "Please enter your email."
                            ]
                        }
                    }
                }
            }
        }
    }

Getting a Single Product Review
--------------------------------

To retrieve the details of a product review you will need to call the ``/api/v1/products/{productCode}/reviews/{id}`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{productCode}/reviews/{id}

+---------------+----------------+-----------------------------------------------------------+
| Parameter     | Parameter type | Description                                               |
+===============+================+===========================================================+
| Authorization | header         | Token received during authentication                      |
+---------------+----------------+-----------------------------------------------------------+
| id            | url attribute  | Identifier of the product review                          |
+---------------+----------------+-----------------------------------------------------------+
| productCode   | url attribute  | Code of product for which the reviews should be displayed |
+---------------+----------------+-----------------------------------------------------------+

Example
^^^^^^^

To see the details of the product review with ``id = 1``, which is defined for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/1 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json"

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 200 OK

.. code-block:: json

    {
        "id": 1,
        "title": "A product review",
        "rating": 3,
        "comment": "This is a comment review",
        "author": {
            "id": 2,
            "email": "test@example.com",
            "emailCanonical": "test@example.com",
            "gender": "u",
            "_links": {
                "self": {
                    "href": "/api/v1/customers/2"
                }
            }
        },
        "status": "new",
        "reviewSubject": {
            "id": 1,
            "name": "MUG-TH",
            "code": "MUG-TH",
            "attributes": [],
            "options": [],
            "associations": [],
            "translations": []
        }
    }

Collection of Product Reviews
------------------------------

To retrieve a paginated list of reviews for a selected product you will need to call the ``/api/v1/products/{productCode}/reviews/`` endpoint with the ``GET`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    GET /api/v1/products/{productCode}/reviews/

+-------------------------------------+----------------+------------------------------------------------------------+
| Parameter                           | Parameter type | Description                                                |
+=====================================+================+============================================================+
| Authorization                       | header         | Token received during authentication                       |
+-------------------------------------+----------------+------------------------------------------------------------+
| productCode                         | url attribute  | Code of product for which the reviews should be displayed  |
+-------------------------------------+----------------+------------------------------------------------------------+
| limit                               | query          | *(optional)* Number of items to display per page,          |
|                                     |                | by default = 10                                            |
+-------------------------------------+----------------+------------------------------------------------------------+
| sorting['nameOfField']['direction'] | query          | *(optional)* Field and direction of sorting,               |
|                                     |                | by default 'desc' and 'createdAt'                          |
+-------------------------------------+----------------+------------------------------------------------------------+

Example
^^^^^^^

To see the first page of all product reviews for the product with ``code = MUG-TH`` use the method below.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/ \
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
        "total": 3,
        "_links": {
            "self": {
                "href": "/api/v1/products/MUG-TH/reviews/?page=1&limit=10"
            },
            "first": {
                "href": "/api/v1/products/MUG-TH/reviews/?page=1&limit=10"
            },
            "last": {
                "href": "/api/v1/products/MUG-TH/reviews/?page=1&limit=10"
            }
        },
        "_embedded": {
            "items": [
                {
                    "id": 4,
                    "title": "A product review",
                    "rating": 3,
                    "comment": "This is a comment review",
                    "author": {
                        "id": 2,
                        "email": "test@example.com",
                        "_links": {
                            "self": {
                                "href": "/api/v1/customers/2"
                            }
                        }
                    },
                    "status": "new",
                    "reviewSubject": {
                        "id": 1,
                        "name": "MUG-TH",
                        "code": "MUG-TH",
                        "options": [],
                        "averageRating": 0,
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "/api/v1/products/MUG-TH"
                            }
                        }
                    },
                    "createdAt": "2017-10-04T20:19:06+03:00",
                    "updatedAt": "2017-10-04T20:19:06+03:00"
                },
                {
                    "id": 3,
                    "title": "A product review 2",
                    "rating": 5,
                    "comment": "This is a comment review 2",
                    "author": {
                        "id": 1,
                        "email": "onetest@example.com",
                        "_links": {
                            "self": {
                                "href": "/api/v1/customers/1"
                            }
                        }
                    },
                    "status": "new",
                    "reviewSubject": {
                        "id": 1,
                        "name": "MUG-TH",
                        "code": "MUG-TH",
                        "options": [],
                        "averageRating": 0,
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "/api/v1/products/MUG-TH"
                            }
                        }
                    },
                    "createdAt": "2017-10-04T18:23:56+03:00",
                    "updatedAt": "2017-10-04T18:44:08+03:00"
                },
                {
                    "id": 1,
                    "title": "Test review 3",
                    "rating": 4,
                    "comment": "This is a comment review 3",
                    "author": {
                        "id": 1,
                        "email": "onetest@example.com",
                        "_links": {
                            "self": {
                                "href": "/api/v1/customers/1"
                            }
                        }
                    },
                    "status": "accepted",
                    "reviewSubject": {
                        "id": 1,
                        "name": "MUG-TH",
                        "code": "MUG-TH",
                        "options": [],
                        "averageRating": 0,
                        "images": [],
                        "_links": {
                            "self": {
                                "href": "/api/v1/products/MUG-TH"
                            }
                        }
                    },
                    "createdAt": "2017-10-03T23:53:24+03:00",
                    "updatedAt": "2017-10-04T19:18:00+03:00"
                }
            ]
        }
    }

Updating Product Review
------------------------

To fully update a product review you will need to call the ``/api/v1/products/{productCode}/reviews/{id}`` endpoint with the ``PUT`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PUT /api/v1/products/{productCode}/reviews/{id}

+---------------+----------------+----------------------------------------------------------+
| Parameter     | Parameter type | Description                                              |
+===============+================+==========================================================+
| Authorization | header         | Token received during authentication                     |
+---------------+----------------+----------------------------------------------------------+
| id            | url attribute  | Product review id                                        |
+---------------+----------------+----------------------------------------------------------+
| productCode   | url attribute  | Code of product for which the reviews should be updated  |
+---------------+----------------+----------------------------------------------------------+
| title         | request        | Product review title                                     |
+---------------+----------------+----------------------------------------------------------+
| comment       | request        | Product review comment                                   |
+---------------+----------------+----------------------------------------------------------+
| rating        | request        | Product review rating (1..5)                             |
+---------------+----------------+----------------------------------------------------------+

Example
^^^^^^^

To fully update the product review with ``id = 1`` for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/1 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PUT \
        --data '
            {
              "title": "A product review",
              "rating": "4",
              "comment": "This is a comment for review"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

To partially update a product review you will need to call the ``/api/v1/products/{productCode}/reviews/{id}`` endpoint with the ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    PATCH /api/v1/products/{productCode}/reviews/{id}

+------------------------------------+----------------+-----------------------------------------------------------+
| Parameter                          | Parameter type | Description                                               |
+====================================+================+===========================================================+
| Authorization                      | header         | Token received during authentication                      |
+------------------------------------+----------------+-----------------------------------------------------------+
| id                                 | url attribute  | Identifier of the product review                          |
+------------------------------------+----------------+-----------------------------------------------------------+
| productCode                        | url attribute  | Code of product for which the reviews should be updated   |
+------------------------------------+----------------+-----------------------------------------------------------+
| title                              | request        | Product review title                                      |
+------------------------------------+----------------+-----------------------------------------------------------+

Example
^^^^^^^

To partially update the product review with ``id = 1`` for the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/1 \
        -H "Authorization: Bearer SampleToken" \
        -H "Content-Type: application/json" \
        -X PATCH \
        --data '
            {
                "title": "This is an another title for the review"
            }
        '

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content

Deleting a Product Review
--------------------------

To delete a product review you will need to call the ``/api/v1/products/{productCode}/reviews/{id}`` endpoint with the ``DELETE`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    DELETE /api/v1/products/{productCode}/reviews/{id}

+---------------+----------------+-----------------------------------------------------------+
| Parameter     | Parameter type | Description                                               |
+===============+================+===========================================================+
| Authorization | header         | Token received during authentication                      |
+---------------+----------------+-----------------------------------------------------------+
| id            | url attribute  | Identifier of the product review                          |
+---------------+----------------+-----------------------------------------------------------+
| productCode   | url attribute  | Code of product for which the reviews should be deleted   |
+---------------+----------------+-----------------------------------------------------------+

Example
^^^^^^^

To delete the product review with ``id = 1`` from the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/1 \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X DELETE

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
    
Accept a Product Review
--------------------------

To accept a product review you will need to call the ``/api/v1/products/{productCode}/reviews/{id}/accept`` endpoint with the ``POST``, ``PUT`` or ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/products/{productCode}/reviews/{id}/accept

+---------------+----------------+-----------------------------------------------------------+
| Parameter     | Parameter type | Description                                               |
+===============+================+===========================================================+
| Authorization | header         | Token received during authentication                      |
+---------------+----------------+-----------------------------------------------------------+
| id            | url attribute  | Identifier of the product review                          |
+---------------+----------------+-----------------------------------------------------------+
| productCode   | url attribute  | Code of product for which the reviews should be accepted  |
+---------------+----------------+-----------------------------------------------------------+

Example
^^^^^^^

To accept the product review with ``id = 1`` from the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/1/accept \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
    
Reject a Product Review
--------------------------

To reject a product review you will need to call the ``/api/v1/products/{productCode}/reviews/{id}/reject`` endpoint with the ``POST``, ``PUT`` or ``PATCH`` method.

Definition
^^^^^^^^^^

.. code-block:: text

    POST /api/v1/products/{productCode}/reviews/{id}/reject

+---------------+----------------+-----------------------------------------------------------+
| Parameter     | Parameter type | Description                                               |
+===============+================+===========================================================+
| Authorization | header         | Token received during authentication                      |
+---------------+----------------+-----------------------------------------------------------+
| id            | url attribute  | Identifier of the product review                          |
+---------------+----------------+-----------------------------------------------------------+
| productCode   | url attribute  | Code of product for which the reviews should be rejected  |
+---------------+----------------+-----------------------------------------------------------+

Example
^^^^^^^

To reject the product review with ``id = 1`` from the product with ``code = MUG-TH`` use the below method.

.. code-block:: bash

    $ curl http://demo.sylius.org/api/v1/products/MUG-TH/reviews/1/reject \
        -H "Authorization: Bearer SampleToken" \
        -H "Accept: application/json" \
        -X POST

Exemplary Response
^^^^^^^^^^^^^^^^^^

.. code-block:: text

    STATUS: 204 No Content
    
