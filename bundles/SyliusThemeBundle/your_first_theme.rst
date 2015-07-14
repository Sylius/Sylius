Your first theme
================

Themes location and definition
------------------------------

Themes should be added to ``app/themes`` directory by default. Each theme should define configuration file called ``theme.json``. 
It's structure has two required parameters - ``name`` and ``logical_name``. Example:

.. code-block:: json

    {
      "name": "Human-readable name",
      "logical_name": "vendor/theme-name"
    }


When adding, modifying or removing theme, it's necessary to rebuild the container (same as adding new translation files in Symfony2) by clearing the cache (`app/console cache:clear`).

Theme structure
---------------

Themes can override and add both bundle resources and app resources. When your theme configuration is in ``SampleTheme/theme.json``, 
app resources should be located at ``SampleTheme/views`` for templates, ``SampleTheme/translations`` for translations and ``SampleTheme/public`` for assets. 
Same comes with the bundle resources, eg. for ``FOSUserBundle`` the paths should be located at ``SampleTheme/FOSUserBundle/views``, 
``SampleTheme/FOSUserBundle/translations`` and ``SampleTheme/FOSUserBundle/public`` respectively.

Enabling themes
---------------

Themes are enabled on runtime, usually by ``kernel.request`` listener, using method ``ThemeContextInterface::setTheme(ThemeInterface $theme)``
- theme context exists as ``sylius.context.theme`` service.

Example of request listener enabling themes:

.. code-block:: php

    namespace Sylius\Bundle\ThemeBundle\Tests\Functional\app\DefaultTestCase;

    use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
    use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
    use Symfony\Component\HttpKernel\Event\GetResponseEvent;
    use Symfony\Component\HttpKernel\HttpKernelInterface;

    class RequestListener
    {
        /**
         * @var ThemeRepositoryInterface
         */
        private $themeRepository;

        /**
         * @var ThemeContextInterface
         */
        private $themeContext;

        /**
         * @param ThemeRepositoryInterface $themeRepository
         * @param ThemeContextInterface $themeContext
         */
        public function __construct(ThemeRepositoryInterface $themeRepository, ThemeContextInterface $themeContext)
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
                $this->themeRepository->findByLogicalName('sylius/cool-theme')
            );
        }
    }


Theme inheritance
-----------------

While you can't set two themes active at once, you can make use of multiple inheritance. Eg.:

.. code-block:: json

    {
        "name": "Child theme",
        "logical_name": "vendor/child-theme",
        "parents": [
            "vendor/first-parent-theme",
            "vendor/seecond-parent-theme"
        ]
    }

.. code-block:: json

    {
        "name": "First parent theme",
        "logical_name": "vendor/first-parent-theme",
        "parents": [
            "vendor/grandparent-theme"
        ]
    }

.. code-block:: json

    {
        "name": "Second parent theme",
        "logical_name": "vendor/second-parent-theme"
    }

.. code-block:: json

    {
        "name": "Grandparent theme",
        "logical_name": "vendor/grandparent-theme"
    }

Configuration showed below will result in given order:

    - Child theme
    - First parent theme
    - Grandparent theme
    - Second parent theme

Grandparent theme gets overrided by first parent theme. First parent theme and second parent theme get overrided by child theme.