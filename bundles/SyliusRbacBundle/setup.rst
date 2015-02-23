Setup
=====

...

Define Permissions
------------------

In your ``app/config/config.yml``, under ``sylius_rbac`` you should define your permissions:

.. code-block:: yaml

    # app/config/config.yml

    sylius_rbac:
        permissions:
            app.product.update: Update product

Configure Basic Roles
---------------------

In your ``app/config/config.yml``, under ``sylius_rbac`` you should configure the roles:

.. code-block:: yaml

    # app/config/config.yml

    sylius_rbac:
        roles:
            app.product_manager:
                name: Product Manager
                permissions: app.product.update

That's it! Now you have to initialize the roles and permission in the database.

Setup Roles and Permissions in the Database
-------------------------------------------

Run the following command:

.. code-block:: bash

    $ app/console sylius:rbac:initialize

Assign roles to the user.
