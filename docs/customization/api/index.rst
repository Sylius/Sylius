Customizing API
===============

We are using the API Platform to create all endpoints in Sylius API.
API Platform allows configuring an endpoint by ``yaml`` and ``xml`` files or by annotations.
In this guide, you will learn how to customize Sylius API endpoints using ``xml`` configuration.

Introduction
------------

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

Also, if you're planning to modify serialization add this code to framework config:

.. code-block:: yaml

    # config/packages/framework.yaml
    #...
    serializer:
        mapping:
            paths: [ '%kernel.project_dir%/config/serialization' ]

.. _how-to-add-and-remove-endpoints:

.. toctree::
    :hidden:

    adding_and_removing_endpoints
    modifying_endpoints
    serialization_customization
    customizing_endpoints_using_yaml

.. include:: /customization/api/map.rst.inc

Learn more
-----------

* `API Platform serialization <https://api-platform.com/docs/core/serialization>`_
