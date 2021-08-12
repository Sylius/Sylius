Using API
=========

Since sylius 1.8 we allow you to use our API.
Sylius API is based on ApiPlatform.
Here are some examples of basic usage for a shop implementation.

Registration
------------

To register a customer all we need to do is single post request:

.. code-block:: bash

    curl -X 'POST' \
        'https://master.demo.sylius.com/api/v2/shop/customers' \
        -H 'accept: */*' \
        -H 'Content-Type: application/ld+json' \
        -d '{
        "firstName": "shop",
        "lastName": "user",
        "email": "shop.user@example.com",
        "password": "pa$$word",
        "subscribedToNewsletter": true
    }'

If we get response code `204`, it means our customer is registered successfully.


Login
-----

Once shop customer is registered, we can now create a login request to get authentication token, which will allow us to use more shop endpoints.
To get token, lets create a simple login request:

.. code-block:: bash

    curl -X 'POST' \
        'https://master.demo.sylius.com/api/v2/shop/authentication-token' \
        -H 'accept: application/json' \
        -H 'Content-Type: application/json' \
        -d '{
        "email": "shop.user@example.com",
        "password": "pa$$word"
    }'

As a response we should get code `200`, along with `token` and `customer` iri.

.. code-block:: json

    {
        "token": "string",
        "customer": "iri"
    }

With token we can add it to header requests and authenticate ourselves to use full potential of sylius API.

.. code-block:: bash

    curl -X 'METHOD' \
        'api-url' \
        -H 'accept: application/ld+json' \
        -H 'Authorization: Bearer string

