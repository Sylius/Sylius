How to add product variants by options to the cart in Sylius API?
=================================================================

In  order to add a product variant to the cart in the Sylius API, you need to fill the "add to cart" request's body with ``productCode`` and ``productVariantCode``
You can find this data in the body of the response which is received after the request below is sent.

.. code-block:: bash

    curl -X GET "https://master.demo.sylius.com/api/v2/shop/products?page=1&itemsPerPage=30" -H  "accept: application/ld+json"

But the most common case is choosing a product variant by its product options.
So below you can find all requests needed to get the product variant of a product with chosen product options values.
In this example, we will be adding an "S" (in size) and "petite" (in height) "Beige strappy summer dress" to the cart.

Firstly you need to get details of the "Beige strappy summer dress" product by its code:

.. code-block:: bash

    curl -X GET "https://master.demo.sylius.com/api/v2/shop/products/Beige_strappy_summer_dress" -H  "accept: application/ld+json"

In the response you will see that this product has 2 product options:

.. code-block:: json

    "options": [
        "/api/v2/shop/product-options/dress_size",
        "/api/v2/shop/product-options/dress_height"
      ],

With this data you can check all the available product option values for each product option:

.. code-block:: bash

    curl -X GET "https://master.demo.sylius.com/api/v2/shop/product-options/dress_size" -H  "accept: application/ld+json"

The response with all the available option values would contain:

.. code-block:: json

    {
        "values": [
            "/api/v2/shop/product-option-values/dress_s",
            "/api/v2/shop/product-option-values/dress_m",
            "/api/v2/shop/product-option-values/dress_l",
            "/api/v2/shop/product-option-values/dress_xl",
            "/api/v2/shop/product-option-values/dress_xxl"
          ],
    }

In the same way, you can check values for the other option - ``dress_height``.

Now, with all necessary data, you can find the "Beige strappy summer dress" product's "small" and "petite" variant
You need to call a GET on the product variants collection with parameters: ``productName`` and with the chosen option values:

.. code-block:: bash

    curl -X GET "https://master.demo.sylius.com/api/v2/shop/product-variants?product=/api/v2/shop/products/Beige_strappy_summer_dress&optionValues[]=/api/v2/shop/product-option-values/dress_height_petite&optionValues[]=/api/v2/shop/product-option-values/dress_s" -H "accept: application/ld+json"

In the response you should get a collection with only one item:

.. code-block:: json

    {
        "hydra:member": [
        {
          "id": 579960,
          "code": "Beige_strappy_summer_dress-variant-0",
          "product": "/api/v2/shop/products/Beige_strappy_summer_dress",
          "optionValues": [
            "/api/v2/shop/product-option-values/dress_s",
            "/api/v2/shop/product-option-values/dress_height_petite"
          ],
          "translations": {
            "en_US": {
              "@id": "/api/v2/shop/product-variant-translation/579960",
              "@type": "ProductVariantTranslation",
              "id": 579960,
              "name": "S Petite",
              "locale": "en_US"
            }
          },
          "price": 7693
    }

.. warning::

    When you search by only some of the product's option values in the response you may get a collection with more than one object.


And with this information, you can add the chosen ``product variant`` to the cart:

.. code-block:: bash

    curl -X PATCH "https://master.demo.sylius.com/api/v2/shop/orders/ORDER_TOKEN/items" -H  "accept: application/ld+json" -H  "Content-Type: application/merge-patch+json"

with body:

.. code-block:: json

    {
        "productCode": "Beige_strappy_summer_dress",
        "productVariantCode": "Beige_strappy_summer_dress-variant-0",
        "quantity": 1
    }
