Customizing Admin routes prefix
===============================

By default, Sylius administration routes are prefixed with ``/admin``.

How to customize Admin routes prefix?
-------------------------------------

You can use the parameter ``sylius_admin.path_name`` to retrieve the admin routes prefix.

.. warning::

    If you used the ``/admin`` prefix in some admin URLs in your code you need replace ``/admin`` by ``/%sylius_admin.path_prefix%``.

.. tip::

    This parameter can be set using ``SYLIUS_ADMIN_ROUTING_PATH_NAME`` environment variable.
