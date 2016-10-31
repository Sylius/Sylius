Customizing Menus
=================

Adding new positions in your menu is done **via events**.

You have got the ``Sylius\Bundle\WebBundle\Event\MenuBuilderEvent`` with ``FactoryInterface`` and ``ItemInterface`` of `KnpMenu`_. So you can manipulate the whole menu.

You've got such events that you should be subscribing to:

.. code-block:: php

    sylius.menu.admin.main                  # Admin Panel menu
    sylius.menu_builder.frontend.main       # Main Shop menu (top bar)
    sylius.menu_builder.frontend.currency   #
    sylius.menu_builder.frontend.taxons     #
    sylius.menu_builder.frontend.social     #
    sylius.menu_builder.frontend.account    #

How to customize a Menu?
------------------------

1. In order to add items to any of the Menus in **Sylius** you have to create a ``AppBundle\EventListener\MenuBuilderListener`` class.

In the example below we are adding a one new item to the Admin panel menu and a one new item to main menu of the shop.

.. code-block:: php

    <?php

    namespace AppBundle\EventListener;

    use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
    use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent as FrontendMenuBuilderEvent;

    class MenuBuilderListener
    {
        /**
         * @param MenuBuilderEvent $event
         */
        public function addBackendMenuItems(MenuBuilderEvent $event)
        {
            $menu = $event->getMenu();

            $menu->addChild('backend_main')
                ->setLabel('Test Backend Main');
        }

        /**
         * @param FrontendMenuBuilderEvent $event
         */
        public function addFrontendMenuItems(FrontendMenuBuilderEvent $event)
        {
            $menu = $event->getMenu();

            $menu->addChild('frontend')
                ->setLabel('Frontend Menu Item');
        }
    }

2. After creating your class with proper methods for all the menu customizations you need, subscribe your
listener to proper events in the ``AppBundle/Resources/config/services.yml``.

.. code-block:: yaml

    services:
        app.admin.menu_builder_listener:
            class: AppBundle\EventListener\MenuBuilderListener
            tags:
                - { name: kernel.event_listener, event: sylius.menu.admin.main, method: addBackendMenuItems }
                - { name: kernel.event_listener, event: sylius.menu_builder.frontend.currency, method: addFrontendMenuItems }

.. _KnpMenu: https://github.com/KnpLabs/KnpMenu
