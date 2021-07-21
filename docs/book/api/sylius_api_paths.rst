Sylius API paths
================

All paths in new API have the same prefix structure: ``/api/v2/admin/`` or ``/api/v2/shop/``
The ``/api/v2`` prefix part indicates the API version and the ``/admin/`` or ``/shop/`` prefixes are necessary for authorization purposes.
When you are adding a new path to API resource configuration, you should remember to add also proper prefix.

You can declare the entire path for each operation (without ``/api/v2/`` as this part is configured globally):

.. code-block:: xml

    <collectionOperation name="admin_get">
        <attribute name="method">GET</attribute>
        <attribute name="path">admin/orders</attribute>
    </collectionOperation>

or you can add a proper prefix for all paths in the chosen resource:

.. code-block:: xml

    <attribute name="route_prefix">shop</attribute>

.. note::

    In some situations, you may need to add a path with a custom structure, in this case, you will probably need to
    configure also the appropriate access in the security file (``config/security.yaml``)
