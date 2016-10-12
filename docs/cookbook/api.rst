How to add a custom model?
==========================

In some cases you may be needing to add new models to your application in order to cover unique business needs.
The proccess of extending Sylius with new entities is simple and intuitive.

As an example we will take a **Supplier entity**, which may be really useful for shop maintenance.

Authenitication
---------------

Creating OAuth client:

.. code-block:: bash

    $ php app/console sylius:oauth-server:create-client --grant-type="password" --grant-type="refresh_token" --grant-type="token"

It will give you such a response:

.. code-block:: bash

    A new client with public id XYZ, secret ABC has been added

Run your application on a built-in server:

.. code-block:: bash

    $ php ap/console server:start localhost:8000

To obtain authorization token for the ``api@example.com`` user with password ``sylius-api`` run:

.. code-block:: bash

    $ curl http://localhost:8000/api/oauth/v2/token -d "client_id"=XYZ -d "client_secret"=ABC -d "grant_type"=password -d "username"=api@example.com -d "password"=sylius-api

This will give you such a response:

.. code-block:: bash

    {"access_token":"DEF","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"GHI"}

Learn more
----------

* :doc:`API Guide </api/index>`
* :doc:`ResourceBundle documentation </bundles/SyliusResourceBundle/index>`
* :doc:`Customization Guide </customization/index>`
