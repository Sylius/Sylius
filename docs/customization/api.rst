Customizing API
===============

For create all endpoints in Sylius API we are using the API Platform.
API Platform allows to configure endpoint by ``yaml`` and ``xml`` files or in ``annotations``.
In these guide we will show you how to customize Sylius API endpoint with ``xml``.

How to prepare project to customization?
========================================

For any kind of customization firstly you need to configure location of your configs.

.. code-block:: yaml
    # config/packages/api_platform.yaml
    api_platform:
    mapping:
        paths:
            - '%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources'
            - '%kernel.project_dir%/config/api_platform'
    patch_formats:
        json: ['application/merge-patch+json']
    swagger:
        versions: [3]

And now if you want to customize any API resource you need copy entire configuration of this resource from ``%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/`` to ``%kernel.project_dir%/config/api_platform``

How to add an additional endpoint to a resource?
================================================

Let's assume that you want to add new endpoint to ``order`` which will dispatch a command.
You need to add proper configuration in ``config/api_platform/Order.xml``:

.. code-block:: xml

    <collectionOperations>
        <collectionOperation name="custom_operation">
            <attribute name="method">POST</attribute>
            <attribute name="path">/shop/order/custom-operation</attribute>
            <attribute name="messenger">input</attribute>
            <attribute name="input">App\Command\CustomCommand</attribute>
        </collectionOperation>
    </collectionOperations>

And that's all, now you have new endpoint with your custom logic.

.. tip::

    Read more about API Platform endpoint configuration `here <https://api-platform.com/docs/core/operations/>`_

How to remove a resource endpoint?
==================================

Let's assume that your shop offer only digital products so you don't need setting shipment method at all.
To remove endpoint you only need to delete unnecessary configuration from ``config/api_platform/Order.xml``

How to rename the resource endpoint's path?
===========================================

If you want to change endpoint's path, only what you need to do is change ``path`` attribute in your config:

.. code-block:: xml

    <itemOperations>
            <itemOperation name="admin_get">
                <attribute name="method">GET</attribute>
                <attribute name="path">/admin/orders/renamed-path/{id}</attribute>
            </itemOperation>
    </itemOperations>

How to modify the endpoints prefixes? "shop" to "retail" rename.
===============================================================

Let's assume that you want to have your own prefixes on paths (for example to be more consistent with rest of your application)
As a first step you need to change ``paths`` or ``route_prefix`` attribute in all needed resources.
Next step is modify security configuration in ``config/packages/security.yaml``, you need to overwrite parameter:

.. code-block:: xml

    parameters:
        sylius.security.new_api_shop_route: "%sylius.security.new_api_route%/retail"
