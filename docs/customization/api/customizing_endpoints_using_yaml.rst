Configuring endpoints using yaml
--------------------------------

To remove an endpoint from the API using YAML you need to specify the operation of which resource
should be removed in ``config/api_platform/config.yaml``.

If you want to remove, for example, the admin ``GET`` endpoint of ``Zones``, you need to configure the ``enabled`` key in its yaml config.

.. code-block:: yaml

    '%sylius.model.zone.class%':
        collectionOperations:
            admin_get:
                enabled: false


Using the ``enabled`` key you can also remove filters you don't need.

.. code-block:: yaml

    '%sylius.model.product.class%':
        collectionOperations:
            shop_get:
                filters:
                    enabled: false

If you need to add a new filter in the yaml configuration, simply add this kind of code to ``config/api_platform/config.yaml``.

.. code-block:: yaml

    '%sylius.model.product.class%':
        collectionOperations:
            shop_get:
                filters:
                    - app.product_new_filter


To add a new operation, just specify it in the config file.

.. code-block:: yaml

    '%sylius.model.channel.class%':
        collectionOperations:
            my_new_operation:
                method: GET
                path: /shop/channels
                normalization_context:
                    groups: ['shop:channel:read']

You can also overwrite existing endpoints, for example let's change admin_get operation in order collectionOperations.

.. code-block:: yaml

    '%sylius.model.order.class%':
        collectionOperations:
            admin_get:
                path: /admin/orders/new_endpoint
                normalization_context:
                    groups: ['shop:channel:new_group']

This way we can edit the existing endpoint and add custom normalization or change path.

.. warning::

    By removing subresource operations, API Platform creates its own endpoints to a given subresource without our firewall.
    To disable endpoint completely we need to overwrite xml configuration.

To remove subresource from Sylius, create new xml file with copied resource class and remove its subresource operations, for example Country.

.. code-block:: xml

    <resources xmlns="https://api-platform.com/schema/metadata"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="https://api-platform.com/schema/metadata https://api-platform.com/schema/metadata/metadata-2.0.xsd"
    >
        <resource class="%sylius.model.country.class%" shortName="Country">
        ...
        -    <subresourceOperations>
        -        <subresourceOperation name="provinces_get_subresource">
        -            <attribute name="method">GET</attribute>
        -            <attribute name="path">/admin/countries/{code}/provinces</attribute>
        -        </subresourceOperation>
        -    </subresourceOperations>
        ...
        </resource>
    </resources>
