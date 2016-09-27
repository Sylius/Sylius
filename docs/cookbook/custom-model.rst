How to add a custom model?
==========================

In some cases you may be needing to add new models to your application in order to cover unique business needs.
The proccess of extending Sylius with new entities is simple and intuitive.

As an example we will take a **Supplier entity**, which may be really useful for th shop maintenance.

1. Define your needs
--------------------

A Supplier needs three essential fields: ``name``, ``description`` and ``enabled`` flag.

2. Generate the entity
----------------------

Sylius provides the `SensioGeneratorBundle <http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html>`_,
that simplifies the process of adding a model.

You need to use such a command in your project directory.

.. code-block:: bash

    $ php app/console generate:doctrine:entity

The generator will ask you for the entity name and fields. See how it should look like to match our assumptions.

.. image:: ../_images/generating_entity.png
    :align: center

3. Update the database using migrations
---------------------------------------

Assuming that you have the databease updated to the state before adding the new entity you should run:

.. code-block:: bash

    $ php app/console doctrine:migrations:diff

This will generate a new migration file which adds the Supplier entity to your database.
Then update the database using the generated migration:

.. code-block:: bash

    $ php app/console doctrine:migrations:migrate

4. Add ResourceInterface to your model class
--------------------------------------------

Go to the generated class file and make it implement the ``ResourceInterface``:

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Sylius\Component\Resource\Model\ResourceInterface;

    class Supplier implements ResourceInterface
    {
        // ...
    }

5. Register your entity as a Sylius resource
--------------------------------------------

If you don't have it yet create a file ``app/config/resources.yml``, import it in the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "resources.yml" }

And add these few lines in the ``resources.yml`` file:

.. code-block:: yaml

    # app/config/resources.yml
    sylius_resource:
        resources:
            app.supplier:
                driver: doctrine/orm
                classes:
                    model: AppBundle\Entity\Supplier

To check if the process was run correctly run such a command:

.. code-block:: bash

    $ php app/console debug:container | grep supplier

The output should be:

.. image:: ../_images/container_debug_supplier.png
    :align: center

6. Use Sylius API to create new resource
----------------------------------------

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

Use the ``access_token`` in the request that will create a new Supplier.

.. code-block:: bash

    $ curl -i -X POST -H "Content-Type: application/json" -H "Authorization: Bearer DEF" -d '{"name": "Example", "description": "Lorem ipsum", "enabled": true}' http://localhost:8000/api/suppliers/

.. tip::

    Read more about authorizing in API :doc:`here </api/authorization>`. Different requests to Sylius API are described in `the Lionframe docs <http://lakion.com/lionframe>`_.

7. Define grid structure for the new entity
-------------------------------------------

...

8. Define routing for entity administration
-------------------------------------------

...

9. Check the admin panel for your changes
-----------------------------------------

Learn more
----------

* ...
