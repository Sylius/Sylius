How to customize Admin routes prefix?
=====================================

By default, Sylius administration routes are prefixed with ``/admin``.

You can use the parameter ``sylius_admin.path_name`` to retrieve the admin routes prefix.

In order to change to administration panel route prefix you need to modify the ``SYLIUS_ADMIN_ROUTING_PATH_NAME`` environment variable.

.. warning::

    If you used the ``/admin`` prefix in some admin URLs in your custom code you need to replace the ``/admin``
    by ``/%sylius_admin.path_prefix%``.
