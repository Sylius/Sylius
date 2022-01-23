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

.. code-block:: yaml

    parameters:
        sylius.security.new_api_shop_route: "%sylius.security.new_api_route%/retail"

.. warning::

    Changing prefix without security configuration update can expose confidential data (like customers addresses).

After these two steps you can start to use endpoints with new prefixes.
