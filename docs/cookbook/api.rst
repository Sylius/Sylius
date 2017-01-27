How to use Sylius API?
======================

In some cases you may be needing to manipulate the resources of your application via its API.
This guide aims to introduce you to the world of Sylius API. For more sophisticated examples and cases follow the :doc:`API Guide </api/index>`.

Authentication
--------------

Creating OAuth client:

.. code-block:: bash

    $ php bin/console sylius:oauth-server:create-client --grant-type="password" --grant-type="refresh_token" --grant-type="token"

It will give you such a response:

.. code-block:: bash

    A new client with public id XYZ, secret ABC has been added

Run your application on a built-in server:

.. code-block:: bash

    $ php bin/console server:start localhost:8000

.. tip::

    Some test fixtures are provided with a default Sylius fixture suite(which can be obtain by executing: ``$ php bin/console sylius:fixtures:load``). By default Sylius will provide following data:
     * Sample user: api@example.com
     * Sample password: sylius-api
     * Sample random client: demo_client
     * Sample client secret: demo_client
     * Sample access token: SampleToken

To obtain authorization token for the default user run:

.. code-block:: bash

    $ curl http://localhost:8000/api/oauth/v2/token -d "client_id"=XYZ -d "client_secret"=ABC -d "grant_type"=password -d "username"=api@example.com -d "password"=sylius-api

This will give you such a response:

.. code-block:: bash

    {"access_token":"DEF","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"GHI"}

Creating a new resource instance via API
----------------------------------------

Use the ``access_token`` in the request that will create a new Supplier (:doc:`that we were creating in another cookbook</cookbook/custom-model>`).

.. code-block:: bash

    $ curl -i -X POST -H "Content-Type: application/json" -H "Authorization: Bearer DEF" -d '{"name": "Example", "description": "Lorem ipsum", "enabled": true}' http://localhost:8000/api/suppliers/

.. tip::

    Read more about authorizing in API :doc:`here </api/authorization>`.

Viewing a single resource instance via API
------------------------------------------

If you would like to show details of a resource use this command with object's id as ``{id)``.
Remember to use **the authorization token**.

Assuming that you have created a supplier in the previous step, it will have id = 1.

.. code-block:: bash

    $ curl -i -H "Authorization: Bearer DEF" http://localhost:8000/api/suppliers/{id}

Viewing an index of resource via API
------------------------------------

If you would like to see a list of all instances of your resource use such a command (provide the authorization token!):

.. code-block:: bash

    $ curl -i -H "Authorization: Bearer DEF" http://localhost:8000/api/suppliers/

Updating a single resource instance via API
-------------------------------------------

If you would like to modify the whole existing resource use such a command (with a valid authorization token of course):

.. code-block:: bash

    $ curl -i -X PUT -H "Content-Type: application/json" -H "Authorization: Bearer DEF" -d '{"name": "Modified Name", "description": "Modified description", "enabled": false}' http://localhost:8000/api/suppliers/1

Partially updating a single resource instance via API
-----------------------------------------------------

If you would like to update just one field of a resource use the PATCH method with such a command:

.. code-block:: bash

    $ curl -i -X PATCH -H "Content-Type: application/json" -H "Authorization: Bearer DEF" -d '{"enabled": true}' http://localhost:8000/api/suppliers/1

Deleting a single resource instance via API
-------------------------------------------

To delete a resource instance you need to call such a command (with an authorization token):

.. code-block:: bash

    $ curl -i -X DELETE -H "Authorization: Bearer DEF" http://localhost:8000/artists/1

Learn more
----------

* :doc:`API Guide </api/index>`
* :doc:`ResourceBundle documentation </bundles/SyliusResourceBundle/index>`
* :doc:`Customization Guide </customization/index>`
* `The Lionframe docs <http://lakion.com/lionframe>`_
