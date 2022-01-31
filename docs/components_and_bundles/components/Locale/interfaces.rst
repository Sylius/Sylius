.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_locale_model_locale-interface:

LocaleInterface
~~~~~~~~~~~~~~~

This interface should be implemented by models representing a single **Locale**.

.. note::
    This interface extends `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_ and
    `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_locale_model_locales-aware-interface:

LocalesAwareInterface
~~~~~~~~~~~~~~~~~~~~~

This interface provides basic operations for locale management.
If you want to have locales in your model just implement this interface.

Service Interfaces
------------------

.. _component_locale_model_locale-context-interface:

LocaleContextInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by the service responsible for managing the current locale.

.. _component_locale_model_locale-provider-interface:

LocaleProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by the service responsible for providing you with a list of available locales.
