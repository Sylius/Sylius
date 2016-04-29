Interfaces
==========

Model Interfaces
----------------

.. _component_translation_model_translatable-interface:

TranslatableInterface
~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a model used in more than one language.

.. hint::
   Although you can implement this interface in your class, it's easier to just
   extend the :ref:`component_translation_model_abstract-translatable` class.

.. note::
   For more detailed information go to `Sylius API TranslatableInterface`_.

.. _Sylius API TranslatableInterface: http://api.sylius.org/Sylius/Component/Translation/Model/TranslatableInterface.html

.. _component_translation_model_translation-interface:

TranslationInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a model responsible for keeping a single translation.

.. hint::
   And as above, although you are completely free to create your own class implementing this interface,
   it's already implemented in the :ref:`component_translation_model_abstract-translation` class.

.. note::
   For more detailed information go to `Sylius API TranslationInterface`_.

.. _Sylius API TranslationInterface: http://api.sylius.org/Sylius/Component/Translation/Model/TranslationInterface.html

Service Interfaces
------------------

.. _component_translation_provider_locale-provider-interface:

LocaleProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service responsible for managing locales.

.. note::
   For more detailed information go to `Sylius API LocaleProviderInterface`_.

.. _Sylius API LocaleProviderInterface: http://api.sylius.org/Sylius/Component/Translation/Provider/LocaleProviderInterface.html

.. _component_translation_repository_translatable-resource-repository-interface:

TranslatableResourceRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a repository responsible for keeping the **LocaleProvider**
and an array of fields

This interface expects you to implement a way of setting an instance of **LocaleProviderInterface**,
and an array of translatable fields into your custom repository.

.. note::
   This interface extends the :ref:`component_resource_repository_repository-interface`.

   For more detailed information go to `Sylius API TranslatableResourceRepositoryInterface`_.

.. _Sylius API TranslatableResourceRepositoryInterface: http://api.sylius.org/Sylius/Component/Translation/Repository/TranslatableResourceRepositoryInterface.html
