Your first theme
================

This tutorial assumes that the :doc:`filesystem <configuration_sources/filesystem>` source.
Make sure it's enabled with the default options:

.. code-block:: yaml

    sylius_theme:
        sources:
            filesystem: ~


Themes location and definition
------------------------------

Private themes should be added to ``app/themes`` directory by default. Every theme should have a default configuration
located in ``composer.json`` file. The only required parameter is ``name``, but it is worth to define other ones
(:doc:`have a look at theme configuration reference <theme_configuration_reference>`).

.. code-block:: json

    {
      "name": "vendor/default-theme"
    }

When adding or removing a theme, it's necessary to rebuild the container (same as adding new translation files in Symfony) by clearing the cache (``bin/console cache:clear``).

Theme structure
---------------

Themes can override and add both bundle resources and app resources. When your theme configuration is in ``SampleTheme/theme.json``,
app resources should be located at ``SampleTheme/views`` for templates, ``SampleTheme/translations`` for translations and ``SampleTheme/public`` for assets.
Same comes with the bundle resources, eg. for ``FOSUserBundle`` the paths should be located at ``SampleTheme/FOSUserBundle/views``,
``SampleTheme/FOSUserBundle/translations`` and ``SampleTheme/FOSUserBundle/public`` respectively.

.. code-block:: text

    AcmeTheme
    ├── AcmeBundle
    │   ├── public
    │   │   └── asset.jpg
    │   ├── translations
    │   │   └── messages.en.yml
    │   └── views
    │       └── template.html.twig
    ├── composer.json
    ├── translations
    │   └── messages.en.yml
    └── views
        └── template.html.twig

Enabling themes
---------------

Themes are enabled on the runtime and uses the theme context to define which one is currently used.
There are two ways to enable your theme:

Custom theme context
~~~~~~~~~~~~~~~~~~~~

Implement ``Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface``, register it as a service and replace the default
theme context with the new one by changing ThemeBundle configuration:

.. code-block:: yaml

    sylius_theme:
        context: acme.theme_context # theme context service id

Request listener and settable theme context
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Create an event listener and register it as listening for ``kernel.request`` event.

.. code-block:: php

    use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
    use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
    use Symfony\Component\HttpKernel\Event\GetResponseEvent;
    use Symfony\Component\HttpKernel\HttpKernelInterface;

    final class ThemeRequestListener
    {
        /**
         * @var ThemeRepositoryInterface
         */
        private $themeRepository;

        /**
         * @var SettableThemeContext
         */
        private $themeContext;

        /**
         * @param ThemeRepositoryInterface $themeRepository
         * @param SettableThemeContext $themeContext
         */
        public function __construct(ThemeRepositoryInterface $themeRepository, SettableThemeContext $themeContext)
        {
            $this->themeRepository = $themeRepository;
            $this->themeContext = $themeContext;
        }

        /**
         * @param GetResponseEvent $event
         */
        public function onKernelRequest(GetResponseEvent $event)
        {
            if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
                // don't do anything if it's not the master request
                return;
            }

            $this->themeContext->setTheme(
                $this->themeRepository->findOneByName('sylius/cool-theme')
            );
        }
    }
