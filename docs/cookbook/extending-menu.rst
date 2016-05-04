Extending the menu
==================

You can add entries to the menu via events easily. You get passed a
``Sylius\\Bundle\\WebBundle\\Event\\MenuBuilderEvent`` with ``FactoryInterface`` and ``ItemInterface`` of
`KnpMenu`_. So you can manipulate the whole menu.

Available for the backend and frontend menus, by listening/subscribing to any of the event constants defined in ``Sylius\Bundle\WebBundle\Event\MenuBuilderEvent``.

Example Usage
-------------

.. code-block:: php

    // src/Acme/ReportsBundle/EventListener/MenuBuilderListener.php
    namespace Acme\ReportsBundle\EventListener;

    use Sylius\Bundle\WebBundle\Event\MenuBuilderEvent;

    class MenuBuilderListener
    {
        public function addBackendMenuItems(MenuBuilderEvent $event)
        {
            $menu = $event->getMenu();

            $menu['sales']->addChild('reports', array(
                'route' => 'acme_reports_index',
                'labelAttributes' => array('icon' => 'glyphicon glyphicon-stats'),
            ))->setLabel('Daily and monthly reports');
        }
    }

.. configuration-block::

    .. code-block:: yaml

        services:
            acme_reports.menu_builder:
                class: Acme\ReportsBundle\EventListener\MenuBuilderListener
                tags:
                    - { name: kernel.event_listener, event: sylius.menu_builder.backend.main, method: addBackendMenuItems }
                    - { name: kernel.event_listener, event: sylius.menu_builder.backend.sidebar, method: addBackendMenuItems }

    .. code-block:: xml

        <!-- src/Acme/ReportsBundle/Resources/config/services.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

            <services>
                <service id="acme_reports.menu_builder" class="Acme\ReportsBundle\EventListener\MenuBuilderListener">
                    <tag name="kernel.event_listener" event="sylius.menu_builder.backend.main" method="addBackendMenuItems" />
                    <tag name="kernel.event_listener" event="sylius.menu_builder.backend.sidebar" method="addBackendMenuItems" />
                </service>
            </services>
        </container>

    .. code-block:: php

        // src/Acme/ReportsBundle/Resources/config/services.php
        use Symfony\Component\DependencyInjection\Definition;

        $definition = new Definition('Acme\ReportsBundle\EventListener\MenuBuilderListener');
        $definition->->addTag('kernel.event_listener', array('event' => 'sylius.menu_builder.backend.main', 'method' => 'addBackendMenuItems'));
        $definition->->addTag('kernel.event_listener', array('event' => 'sylius.menu_builder.backend.sidebar', 'method' => 'addBackendMenuItems'));

        $container->setDefinition('acme_reports.menu_builder', $definition);

.. _KnpMenu: https://github.com/KnpLabs/KnpMenu
