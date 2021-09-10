.. index::
   single: Catalog Promotions

Catalog Promotions
==================

The **Catalog Promotions** system in **Sylius** is a new way of dealing with promotions on multiple products.
If you get used to `Cart Promotions </book/orders/cart-promotions>` this will be something familiar to you.

It is managed by combination of promotion rules and actions, where you can specify on which f.e. products or taxons
you can specify the Catalog Promotion with your custom actions as well as actions like percentage or value discount.

You can also specify the exact dates from which to which the catalog will be applied, as well as assigning the needed channels.

Catalog Promotion Parameters
----------------------------

Catalog Promotion has a few basic parameters that represents it - a unique ``code`` and ``name``:

.. note::

    The parameter ``code`` should contain only letters, numbers, dashes and underscores.
    We encourage you to use ``snake_case`` codes.

.. code-block:: bash

    {
        "code": "catalog_promotion" # unique
        "name": "catalog promotion"
        # ...
    }

Rest of the fields are used for configuration:

* **Channels** are used to define channels on which given promotion is applied:

.. code-block:: bash

    {
        #...
        "channels": [
            "/api/v2/admin/channels/FASHION_WEB", #IRI
            "/api/v2/admin/channels/HOME_WEB"
        ]
        # ...
    }

* **Rules** are used to define rules on which the catalog promotion will work:

.. code-block:: bash

    {
        #...
        "rules": [
            {
                "type": "for_variants",
                "configuration": {
                    "variants": [
                        "Everyday_white_basic_T_Shirt-variant-1", #Variant Code
                        "Everyday_white_basic_T_Shirt-variant-4"
                    ]
                }
            }
        ]
        # ...
    }

For possible rules see `Catalog Promotion Rules configuration reference`_

* **Actions** are used to defined what happens when the promotion is applied:

.. code-block:: bash

    {
        #...
        "actions": [
            {
                "type": "percentage_discount",
                "configuration": {
                    "amount": 0.5 #float
                }
            }
        ]
        # ...
    }

* **Translations** are used to define labels and descriptions for languages you are configuring:

.. code-block:: bash

    {
        #...
        "translations": {
            "en_US": {
                "label": "Summer discount",
                "description": "The grass so green, the sun so bright. Life seems a dream, no worries in sight.",
                "locale": "en_US" #Locale Code
                }
            }
        }
        # ...
    }

How to create a Catalog Promotion?
----------------------------------

After we get to know with some basics of Catalog Promotion let's see how we can create one:

* **API** The common use case is to make it through API, first you need to authorize yourself as an admin (you don't want to let a guest create it - don't you?).

.. tip::

    Check this doc `Authorization </book/api/authorization>` if you are having trouble with login in.

And let's call the POST endpoint to create very basic catalog promotion:

.. code-block:: bash

    curl -X 'POST' \
      'https://hostname/api/v2/admin/catalog-promotions' \
      -H 'accept: application/ld+json' \
      -H 'Authorization: Bearer authorizationToken' \
      -H 'Content-Type: application/ld+json' \
      -d '{
        "code": "catalog_promotion",
        "name": "catalog promotion"
        }'


If everything was fine, the server will respond with 201 status code.
This means you have created a simple catalog promotion with ``name`` and ``code`` only.

You can check if the catalog promotion exists by using GET endpoint
.. code-block:: bash

    curl -X 'GET' \
    'https://hostname/api/v2/admin/catalog-promotions'

* **Programmatically** Similar to cart promotions you can use factory to create a new catalog promotion:

.. code-block:: php

   /** @var CatalogPromotionInterface $promotion */
   $promotion = $this->container->get('sylius.factory.catalog_promotion')->createNew();

   $promotion->setCode('catalog_promotion');
   $promotion->setName('catalog promotion');

.. note::

    Take into account that both the API and Programmatically added catalog promotions in this shape are not really useful.
    You need to add configurations to them so they make any business valued changes.

TODO create rule, action, and some of thier description. Possible more API usage.


Catalog Promotion Rules configuration reference
'''''''''''''''''''''''''''''''''''''''''''''''

+-------------------------------+--------------------------------------------------------------------+
| Rule type                     | Rule Configuration Array                                           |
+===============================+====================================================================+
| ``for_variants``              | ``['variants' => [$variantCodes]]``                                |
+-------------------------------+--------------------------------------------------------------------+

Catalog Promotion Actions configuration reference
'''''''''''''''''''''''''''''''''''''''''''''''''

+-------------------------------+--------------------------------------------------------------------+
| Action type                   | Action Configuration Array                                         |
+===============================+====================================================================+
| ``percentage_discount``       | ``['amount' => $amountFloat]``                                     |
+-------------------------------+--------------------------------------------------------------------+


Learn more
----------

* :doc:`Cart Promotions </book/orders/cart-promotions>`
