.. index::
   single: Catalog Promotions

Catalog Promotions
==================

The **Catalog Promotions** system in **Sylius** is a new way of dealing with promotions on multiple products.
If you get used to :doc:`Cart Promotions </book/orders/cart-promotions>` this will be something familiar to you.

It is managed by combination of promotion scopes and actions, where you can specify on which e.g. products or taxons
you can specify the Catalog Promotion with your custom actions as well as actions like percentage discount.

It is possible to set start and end date for Catalog Promotions, their priority (Promotion will be applied on descending order of priority).
You can also set exclusiveness of promotion, in this case only one catalog promotion will be applied - exclusive one with highest priority.

You can assign the needed channels too.

.. warning::

    Be aware that processing a big catalog of products can be time consuming.
    Please consider a 2-10 minutes delay starting from the specified dates (it depends how big catalog you have).

Catalog Promotion Parameters
----------------------------

Catalog Promotion has a few basic parameters that represent it - a unique ``code`` and ``name``:

.. note::

    The parameter ``code`` should contain only letters, numbers, dashes and underscores (like all codes in Sylius).
    We encourage you to use ``snake_case`` codes.

.. code-block:: bash

    {
        "code": "t_shirt_promotion" # unique
        "name": "T-shirt Promotion"
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

* **Scopes** are used to define scopes on which the catalog promotion will work:

.. code-block:: bash

    {
        #...
        "scopes": [
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

.. note::

    The usage of Variant Code over IRI is in this case dictated by the kind of relationship.
    Here it is a part of configuration, where e.g. channel is a relation to the resource.

For possible scopes see `Catalog Promotion Scopes configuration reference`_

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

    Check this doc :doc:`Authorization </book/api/authorization>` if you are having trouble with login in.

And let's call the POST endpoint to create very basic catalog promotion:

.. code-block:: bash

    curl -X 'POST' \
      'https://hostname/api/v2/admin/catalog-promotions' \
      -H 'accept: application/ld+json' \
      -H 'Authorization: Bearer authorizationToken' \
      -H 'Content-Type: application/ld+json' \
      -d '{
        "code": "t_shirt_promotion",
        "name": "T-shirt Promotion"
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
   $promotion = $this->container->get('sylius.factory.t_shirt_promotion')->createNew();

   $promotion->setCode('t_shirt_promotion');
   $promotion->setName('T-shirt Promotion');

.. note::

    Take into account that both the API and Programmatically added catalog promotions in this shape are not really useful.
    You need to add configurations to them so they make any business valued changes.

.. _how-to-create-a-catalog-promotion-scope-and-action:

How to create a Catalog Promotion Scope and Action?
---------------------------------------------------

The creation of Catalog Promotion was quite simple but at this shape it has no real functionality. Let's add scope and action:

In API we will extend last command:

.. code-block:: bash

    curl -X 'POST' \
      'https://hostname/api/v2/admin/catalog-promotions' \
      -H 'accept: application/ld+json' \
      -H 'Authorization: Bearer authorizationToken' \
      -H 'Content-Type: application/ld+json' \
      -d '{
        "code": "t_shirt_promotion",
        "name": "T-shirt Promotion",
        "channels": [
            "/api/v2/admin/channels/FASHION_WEB"
        ],
        "scopes": [
            {
              "type": "for_variants",
              "configuration": {
                "variants": ["Everyday_white_basic_T_Shirt-variant-1", "Everyday_white_basic_T_Shirt-variant-4"]
              }
            }
          ],
          "actions": [
            {
              "type": "percentage_discount",
              "configuration": {
                "amount": 0.5
              }
            }
          ],
          "translations": {
            "en_US": {
              "label": "T-shirt Promotion",
              "description": "T-shirt Promotion description",
              "locale": "en_US"
            }
        }'

This will create a catalog promotions with relations to Scope ``for_variants``, Action ``percentage_discount`` and also
translation for ``en_US`` locale.

We can also make it programmatically:

.. code-block:: php

    /** @var CatalogPromotionInterface $catalogPromotion */
    $catalogPromotion = $this->container->get('sylius.factory.catalog_promotion')->createNew();
    $catalogPromotion->setCode('t_shirt_promotion');
    $catalogPromotion->setName('T-shirt Promotion');

    $catalogPromotion->setCurrentLocale('en_US');
    $catalogPromotion->setFallbackLocale('en_US');
    $catalogPromotion->setLabel('T-shirt Promotion');
    $catalogPromotion->setDescription('T-shirt Promotion description');

    $catalogPromotion->addChannel('FASHION_WEB');

    /** @var CatalogPromotionScopeInterface $catalogPromotionScope */
    $catalogPromotionScope = $this->catalogPromotionScopeExampleFactory->create($scope);
    $catalogPromotionScope->setCatalogPromotion($catalogPromotion);
    $catalogPromotion->addScope($catalogPromotionScope);

    /** @var CatalogPromotionActionInterface $catalogPromotionAction */
    $catalogPromotionAction = $this->catalogPromotionActionExampleFactory->create($action);
    $catalogPromotionAction->setCatalogPromotion($catalogPromotion);
    $catalogPromotion->addAction($catalogPromotionAction);

    /** @var MessageBusInterface $eventBus */
    $eventBus = $this->container->get('sylius.event_bus');
    $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));

And now you should be able to see created Catalog Promotion. You can check if it exists like in the last example (with GET endpoint).
If you look into ``product-variant`` endpoint in shop you should see now that chosen variants have lowered price and added field ``appliedPromotions``:

.. code-block:: bash

    curl -X 'GET' \
    'https://hostname/api/v2/shop/product-variant/Everyday_white_basic_T_Shirt-variant-1'

.. code-block:: bash

    # response content
    {
        "@context": "/api/v2/contexts/ProductVariant",
        "@id": "/api/v2/shop/product-variants/Everyday_white_basic_T_Shirt-variant-1",
        # ...
        "price": 2000,
        "originalPrice": 4000,
        "appliedPromotions": {
            "T-shirt Promotion": {
                "name": "T-shirt Promotion",
                "description": "T-shirt Promotion description"
            }
        },
        "inStock": true
    }

.. note::

    If you create a Catalog Promotion programmatically, remember to manually dispatch ``CatalogPromotionUpdated``

Catalog Promotion Scopes configuration reference
''''''''''''''''''''''''''''''''''''''''''''''''

+-------------------------------+--------------------------------------------------------------------+
| Scope type                    | Scope Configuration Array                                          |
+===============================+====================================================================+
| ``for_products``              | ``['products' => [$productCode]]``                                 |
+-------------------------------+--------------------------------------------------------------------+
| ``for_taxons``                | ``['variants' => [$variantCode]]``                                 |
+-------------------------------+--------------------------------------------------------------------+
| ``for_variants``              | ``['taxons' => [$taxonCode]]``                                     |
+-------------------------------+--------------------------------------------------------------------+

Catalog Promotion Actions configuration reference
'''''''''''''''''''''''''''''''''''''''''''''''''

+-------------------------------+--------------------------------------------------------------------+
| Action type                   | Action Configuration Array                                         |
+===============================+====================================================================+
| ``percentage_discount``       | ``['amount' => $amountFloat]``                                     |
+-------------------------------+--------------------------------------------------------------------+

Catalog Promotion asynchronicity
--------------------------------

Applying Catalog Promotion to the product catalog is an asynchronous operation.
It means that new prices will not be updated right after confirmation of creating or updating Catalog Promotion but after some time.
This delay depends on the size of the product catalog in the shop.
Another effect of this approach is the possibility to create Catalog Promotion with the future date (processing will start in given start date).

To make the Catalog Promotion application asynchronously we are using `SymfonyMessenger` and queue provided by `Doctrine`.
After changes in CatalogPromotion, we dispatch proper message with delay calculated from provided dates.

.. warning::

    To enable asynchronous Catalog Promotion, remember about running messenger consumer in a separate process, use the command: ``php bin/console messenger:consume async``
    For more information check official `Symfony docs <https://symfony.com/doc/current/messenger.html#consuming-messages-running-the-worker>`_

How the Catalog Promotions are applied?
---------------------------------------

The Catalog Promotion application process utilises `API Platform events <https://api-platform.com/docs/core/events/>`_ for an API.
and `Resource events </book/architecture/events>`_ for UI. When a new Promotion is created or the existing one is edited
there are services that listen on proper events and dispatch ``CatalogPromotionUpdated`` event to event bus.

This event is handled by `CatalogPromotionUpdateListener <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Listener/CatalogPromotionUpdateListener.php>`_ which resolves the appropriate ``CatalogPromotion``.
With the needed data and configuration from ``CatalogPromotion`` we can now process the ``Product`` and ``ProductVariant`` entities.

The changes are first handled in `CatalogPromotionProcessor <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Processor/CatalogPromotionProcessor.php>`_
which inside uses the `CatalogPromotionApplicator <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Applicator/CatalogPromotionApplicator.php>`_.

The **CatalogPromotionProcessor**'s method ``process()`` is executed on the eligible items:

* firstly it iterates over eligible items: ``Product Variants``,
* then it calculates and applies the ``CatalogPromotionAction`` for given item

.. note::

    If you want to reapply Catalog Promotion manually you can refer to the :ref:`How to create a Catalog Promotion Scope and Action? <how-to-create-a-catalog-promotion-scope-and-action>` section

Learn more
----------

* :doc:`Cart Promotions </book/orders/cart-promotions>`
