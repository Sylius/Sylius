Basic Usage
===========

LocaleContext
-------------

The **LocaleContext** allows you to manage the currently used locale.

.. code-block:: php

    <?php

    use Sylius\Component\Storage\StorageInterface;

    class Storage implements StorageInterface
    {
        /**
         * {@inheritdoc}
         */
        public function hasData($key)
        {
            // TODO: Implement hasData() method.
        }

        /**
         * {@inheritdoc}
         */
        public function getData($key, $default = null)
        {
            // TODO: Implement getData() method.
        }

        /**
         * {@inheritdoc}
         */
        public function setData($key, $value)
        {
            // TODO: Implement setData() method.
        }

        /**
         * {@inheritdoc}
         */
        public function removeData($key)
        {
            // TODO: Implement removeData() method.
        }
    }

.. code-block:: php

    <?php

    use Sylius\Component\Locale\Context\LocaleContext;
    use Sylius\Component\Resource\Repository\InMemoryRepository;

    $storage = new Storage();

    $localeContext = new LocaleContext($storage, 'en_US');

    $localeContext->getDefaultLocale() // Output will be 'en'.
    $localeContext->getCurrentLocale() // Output based on your storage implementation.
    $localeContext->setCurrentLocale('us') // It will set your default locale in your storage.

.. note::
    For more detailed information go to `Sylius API LocaleContext`_.

.. _Sylius API LocaleContext: http://api.sylius.org/Sylius/Component/Locale/Context/LocaleContext.html

LocaleProvider
--------------

The **LocaleProvider** allows you to get all available locales.

.. code-block:: php

    <?php

    use Sylius\Component\Locale\Provider\LocaleProvider;

    $locales = new InMemoryRepository();

    $localeProvider = new LocaleProvider($locales);

    $localeProvider->getAvailableLocales() //Output will be a collection of all enabled locales
    $localeProvider->isLocaleAvailable('en') //It will check if that locale is enabled

.. note::
    For more detailed information go to `Sylius API LocaleProvider`_.

.. _Sylius API LocaleProvider: http://api.sylius.org/Sylius/Component/Locale/Provider/LocaleProvider.html
