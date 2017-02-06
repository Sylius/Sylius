.. note:: 

    In order to add a new locale to your store you have to assign it to a channel.

.. index::
   single: Locales

Locales
=======

To support multiple languages we are using **Locales** in **Sylius**. Locales are language codes standardized by the ISO 15897.

.. tip::

    In the dev environment you can easily check what locale you are currently using in the Symfony debug toolbar:

    .. image:: ../../_images/toolbar.png
        :align: center

Base Locale
-----------

During the :doc:`installation </book/installation/installation>` you provided a default base locale. This is the language in which everything
in your system will be saved in the database - all the product names, texts on website, e-mails etc.

Locale Context
--------------

With the default configuration, customers are able to change the store language in the frontend.

To manage the currently used language, we use the **LocaleContext**. You can always access it with the ID ``sylius.context.locale`` in the container.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $locale = $this->get('sylius.context.locale')->getLocale();
    }

To change the locale use the ``setLocale()`` method of the context service.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $this->get('sylius.context.locale')->setLocale('pl_PL'); // Store will be displayed in Polish.
    }

The locale context can be injected into any of your services and give you access to the currently used locale.

Available Locales Provider
--------------------------

The Locale Provider service (``sylius.locale_provider``) is responsible for returning all languages available for the current user. By default, returns all configured locales.
You can easily modify this logic by overriding this service.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $locales = $this->get('sylius.locale_provider')->getAvailableLocalesCodes();

        foreach ($locales as $locale) {
            echo $locale->getCode();
        }
    }

To get all languages configured in the store, regardless of your availability logic, use the locales repository:

.. code-block:: php

    <?php

    $locales = $this->get('sylius.repository.locale')->findAll();

Learn more
----------

* :doc:`Locale - Component Documentation </components/Locale/index>`.
