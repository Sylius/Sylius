Service interfaces
==================

.. _component_resource_provider_locale-provider-interface:

LocaleProviderInterface
-----------------------

This interface should be implemented by a service responsible for managing locales.

.. note::
   For more detailed information go to `Sylius API LocaleProviderInterface`_.

.. _Sylius API LocaleProviderInterface: http://api.sylius.org/Sylius/Component/Resource/Provider/LocaleProviderInterface.html

.. _component_resource_repository_translatable-resource-repository-interface:

TranslatableRepositoryInterface
-------------------------------

This interface should be implemented by a repository responsible for keeping the **LocaleProvider**
and an array of fields

This interface expects you to implement a way of setting an instance of **LocaleProviderInterface**,
and an array of translatable fields into your custom repository.

.. note::
   This interface extends the :ref:`component_resource_repository_repository-interface`.

   For more detailed information go to `Sylius API TranslatableResourceRepositoryInterface`_.

.. _Sylius API TranslatableResourceRepositoryInterface: http://api.sylius.org/Sylius/Component/Resource/Repository/TranslatableRepositoryInterface.html
