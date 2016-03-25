.. index::
   single: Locales

Locales
=======

To support multiple site languages, we use *Locale* model with the following set of fields:

* id
* code
* enabled
* createdAt
* updatedAt

Locale Context
--------------

With the default configuration, customers are able to change the store language in the frontend.

To manage the currently used language, we use **LocaleContext**. You can always access it through ``sylius.context.locale`` id.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $locale = $this->get('sylius.context.locale')->getLocale();

        echo $locale; // pl_PL
    }

To change the locale, you can simply use the ``setLocale()`` method of the context service.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $this->get('sylius.context.locale')->setLocale('de_DE'); // Store will be displayed in German.
    }

The locale context can be injected into your custom service and give you access to currently used locale.

Available Locales Provider
--------------------------

Service ``sylius.locale_provider`` is responsible for returning all languages available to the current user. By default, it filters out all disabled locales.
You can easily modify this logic by overriding this component.

.. code-block:: php

    <?php

    public function fooAction()
    {
        $locales = $this->get('sylius.locale_provider')->getAvailableLocales();

        foreach ($locales as $locale) {
            echo $locale->getCode();
        }
    }

To get all languages configured in the store, including the disabled ones, you can simply use the repository.

.. code-block:: php

    <?php

    $locales = $this->get('sylius.repository.locale')->findAll();

Final Thoughts
--------------

...

Learn more
----------

* ...
