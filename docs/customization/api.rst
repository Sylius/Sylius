Customizing API
===============

We are using the API Platform to create all endpoints in Sylius API.
API Platform allows configuring an endpoint by ``yaml`` and ``xml`` files or by annotations.
In this guide, you will learn how to customize Sylius API endpoints using ``xml`` configuration.

How to prepare project for customization?
-----------------------------------------

If your project was created before v1.10, make sure your API Platform config follows the one below:

.. code-block:: yaml

    # config/packages/api_platform.yaml
    api_platform:
        mapping:
            paths:
                - '%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources'
                - '%kernel.project_dir%/config/api_platform'
                - '%kernel.project_dir%/src/Entity'
        patch_formats:
            json: ['application/merge-patch+json']
        swagger:
            versions: [3]

How to add an additional endpoint?
----------------------------------

Let's assume that you want to add a new endpoint to the ``Order`` resource that will be dispatching a command.
If you want to customize any API resource, you need to copy the entire configuration of this resource from
``%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/`` to ``%kernel.project_dir%/config/api_platform``.
Add the following configuration in the config copied to ``config/api_platform/Order.xml``:

.. code-block:: xml

    <collectionOperations>
        <collectionOperation name="custom_operation">
            <attribute name="method">POST</attribute>
            <attribute name="path">/shop/orders/custom-operation</attribute>
            <attribute name="messenger">input</attribute>
            <attribute name="input">App\Command\CustomCommand</attribute>
        </collectionOperation>
    </collectionOperations>

And that's all, now you have a new endpoint with your custom logic.

.. tip::

    Read more about API Platform endpoint configuration `here <https://api-platform.com/docs/core/operations/>`_

How to remove an endpoint?
--------------------------

Let's assume that your shop is offering only digital products. Therefore, while checking out,
your customers do not need to choose a shipping method for their orders.

Thus you will need to modify the configuration file of the ``Order`` resource and remove the shipping method choosing endpoint from it.
To remove the endpoint you only need to delete the unnecessary configuration from your ``config/api_platform/Order.xml`` which is a copied configuration file, that overwrites the one from Sylius.

.. code-block:: xml

    <!-- delete this configuration -->
    <itemOperation name="shop_select_shipping_method">
        <!-- ... -->
    </itemOperation>

How to rename an endpoint's path?
---------------------------------

If you want to change an endpoint's path, you just need to change the ``path`` attribute in your config:

.. code-block:: xml

    <itemOperations>
            <itemOperation name="admin_get">
                <attribute name="method">GET</attribute>
                <attribute name="path">/admin/orders/renamed-path/{id}</attribute>
            </itemOperation>
    </itemOperations>

How to modify the endpoints prefixes?
-------------------------------------

Let's assume that you want to have your own prefixes on paths (for example to be more consistent with the rest of your application).
As the first step you need to change the ``paths`` or ``route_prefix`` attribute in all needed resources.
The next step is to modify the security configuration in ``config/packages/security.yaml``, you need to overwrite the parameter:

.. code-block:: xml

    parameters:
        sylius.security.new_api_shop_route: "%sylius.security.new_api_route%/retail"

.. warning::

    Changing prefix without security configuration update can expose confidential data (like customers addresses).

After these two steps you can start to use endpoints with new prefixes.
