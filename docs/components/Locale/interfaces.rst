Interfaces
==========

Model Interfaces
----------------

.. _component_locale_model_locale-interface:

LocaleInterface
~~~~~~~~~~~~~~~

This interface should be implemented by models representing a single **Locale**.

.. note::
    This interface extends :ref:`component_resource_model_code-aware-interface` and
    :ref:`component_resource_model_timestampable-interface`.

    For more detailed information go to `Sylius API LocaleInterface`_.

.. _Sylius API LocaleInterface: http://api.sylius.org/Sylius/Component/Locale/Model/LocaleInterface.html

.. _component_locale_model_locales-aware-interface:

LocalesAwareInterface
~~~~~~~~~~~~~~~~~~~~~

This interface provides basic operations for locale management.
If you want to have locales in your model just implement this interface.

.. note::
    For more detailed information go to `Sylius API LocalesAwareInterface`_.

.. _Sylius API LocalesAwareInterface: http://api.sylius.org/Sylius/Component/Locale/Model/LocalesAwareInterface.html

Service Interfaces
------------------

.. _component_locale_model_locale-context-interface:

LocaleContextInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by the service responsible for managing the current locale.

.. note::
    For more detailed information go to `Sylius API LocaleContextInterface`_.

.. _Sylius API LocaleContextInterface: http://api.sylius.org/Sylius/Component/Locale/Context/LocaleContextInterface.html

.. _component_locale_model_locale-provider-interface:

LocaleProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by the service responsible for providing you with a list of available locales.

.. note::
    For more detailed information go to `Sylius API LocaleProviderInterface`_.

.. _Sylius API LocaleProviderInterface: http://api.sylius.org/Sylius/Component/Locale/Provider/LocaleProviderInterface.html
