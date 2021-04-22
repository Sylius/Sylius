Sylius API
==========

Unified API
-----------

.. warning::

    The new, unified Sylius API is still under development, that's why the whole ``ApiBundle`` is tagged with ``@experimental``.
    This means that all code from ``ApiBundle`` is excluded from :doc:`Backward Compatibility Promise </book/organization/backward-compatibility-promise>`.

To use this API remember to generate JWT token. For more information, please visit `jwt package documentation <https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#generate-the-ssh-keys>`_.

This part of the documentation is about the currently developed unified API for the Sylius platform.

.. toctree::
    :hidden:

    unified_api/index

.. include:: /api/unified_api/map.rst.inc

Admin API
---------

This part of the documentation is about the old Admin API for the Sylius platform.

.. toctree::
    :hidden:

    admin_api/index

.. include:: /api/admin_api/map.rst.inc

Shop API
--------

`Sylius Shop API <https://github.com/Sylius/ShopApiPlugin>`_ is an official plugin, providing customer-centered API.

Follow the `API reference <https://app.swaggerhub.com/apis/Sylius/sylius-shop-api/1.0.0>`_ for more details.
