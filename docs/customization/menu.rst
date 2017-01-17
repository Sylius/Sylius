Customizing Menus
=================

Adding new positions in your menu is done **via events**.

You have got the ``Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent`` with ``FactoryInterface`` and ``ItemInterface`` of `KnpMenu`_, this lets you manipulate the whole menu.

You've got two events that you should be subscribing to:

.. code-block:: php

    sylius.menu.shop.account # For the menu of the MyAccount section in shop
    sylius.menu.admin.main # For the Admin Panel menu

How to customize Admin Menu?
----------------------------

.. tip::

    Admin Panel menu is the one in the left expandable sidebar on the ``/admin/`` url.

**1.** In order to add items to the Admin menu in **Sylius** you have to create a ``AppBundle\Menu\AdminMenuListener`` class.

In the example below we are adding a one new item and subitem to the Admin panel menu.

.. code-block:: php

    <?php

    namespace AppBundle\Menu;

    use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

    final class AdminMenuListener
    {
        /**
         * @param MenuBuilderEvent $event
         */
        public function addAdminMenuItems(MenuBuilderEvent $event)
        {
            $menu = $event->getMenu();

            $newSubmenu = $menu->addChild('new')
                ->setLabel('Custom Admin Submenu');

            $newSubmenu->addChild('new-subitem')
                ->setLabel('Custom Admin Menu Itemu');
        }
    }

**2.** After creating your class with a proper method for the menu customizations you need, subscribe your
listener to the ``sylius.menu.admin.main`` event in the ``app/config/services.yml``.

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.listener.admin.menu_builder:
            class: AppBundle\Menu\AdminMenuListener
            tags:
                - { name: kernel.event_listener, event: sylius.menu.admin.main, method: addAdminMenuItems }

Remember to import the ``app/config/services.yml`` into the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "services.yml" }

**3.** Result:

After these two steps your admin panel menu should look like that, the new items appear at the bottom:

.. image:: ../_images/admin_menu.png
    :align: center

How to customize Account Menu?
------------------------------

.. tip::

    My Account panel menu is the one in the left sidebar on the ``/account/dashboard/`` url.

**1.** In order to add items to the Account menu in **Sylius** you have to create a ``AppBundle\Menu\AccountMenuListener`` class.

In the example below we are adding a one new item to **the menu in the My Account section of shop**.

.. code-block:: php

    <?php

    namespace AppBundle\Menu;

    use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

    final class AdminMenuListener
    {
        /**
         * @param MenuBuilderEvent $event
         */
        public function addAccountMenuItems(FrontendMenuBuilderEvent $event)
        {
            $menu = $event->getMenu();

            $menu->addChild('new', ['route' => 'sylius_shop_account_dashboard'])
                ->setLabel('Custom Account Menu Item')
                ->setLabelAttribute('icon', 'star');
        }
    }

As you can see above the new item can be given a route, a label and an icon.

**2.** After creating your class with a proper method for the menu customizations you need, subscribe your
listener to the ``sylius.menu.shop.account`` event in the ``app/config/services.yml``.

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.listener.shop.menu_builder:
            class: AppBundle\Menu\AccountMenuListener
            tags:
                - { name: kernel.event_listener, event: sylius.menu.shop.account, method: addAccountMenuItems }

Remember to import the ``app/config/services.yml`` into the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "services.yml" }

**3.** Result:

After these two steps your admin panel menu should look like that, the new item appears at the bottom:

.. image:: ../_images/account_menu.png
    :align: center

.. _KnpMenu: https://github.com/KnpLabs/KnpMenu
