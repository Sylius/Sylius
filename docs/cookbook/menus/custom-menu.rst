How to add a custom menu entry?
===============================

In addition to the six events already provided for `menus`_, you can **add your own**. This will allow you to use the ``KnpMenu`` features in your templates for example.

1. Create a MenuBuilderEvent& a MenuBuilder
-------------------------------------------

Create a new class and make it extends the ``MenuBuilderEvent`` class :

.. code-block:: php

    <?php

    namespace App\Event;

    use App\MyBusinessModel\Coupon;
    use Knp\Menu\FactoryInterface;
    use Knp\Menu\ItemInterface;
    use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

    class CouponShowMenuBuilderEvent extends MenuBuilderEvent
    {
        /** @var Coupon */
        private $coupon;

        public function __construct(
            FactoryInterface $factory,
            ItemInterface $menu,
            Coupon $coupon
        )
        {
            parent::__construct($factory, $menu);

            $this->coupon = $coupon;
        }

        /**
        * @return Coupon
        */
        public function getCoupon(): Coupon
        {
            return $this->coupon;
        }
    }
    
Then, create a new final class :

.. code-block:: php

    <?php

    namespace App\Menu;

    use App\MyBusinessModel\Coupon;
    use Knp\Menu\FactoryInterface;
    use Knp\Menu\ItemInterface;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;

    final class CouponShowMenuBuilder
    {
        public const EVENT_NAME = 'sylius.menu.admin.coupon.show';

        /** @var FactoryInterface */
        private $factory;

        /** @var EventDispatcherInterface */
        private $eventDispatcher;

        public function __construct(
            FactoryInterface $factory,
            EventDispatcherInterface $eventDispatcher
        ) {
            $this->factory = $factory;
            $this->eventDispatcher = $eventDispatcher;
        }

        public function createMenu(array $options): ItemInterface
        {
            $menu = $this->factory->createItem('root');

            if (!isset($options['coupon'])) {
                return $menu;
            }

            $coupon = $options['coupon'];
            $this->addChildren($menu, $coupon);

            $this->eventDispatcher->dispatch(
                self::EVENT_NAME,
                new CouponShowMenuBuilderEvent($this->factory, $menu, $coupon)
            );

            return $menu;
        }

        private function addChildren(ItemInterface $menu, Coupon $coupon): void
        {
            $menu
                ->addChild('coupon_validate', [
                    'route' => 'app_coupon_validate',
                    'routeParameters' => ['id' => $coupon->getId()],
                ])
                ->setAttribute('type', 'link')
                ->setLabel('sylius.coupon.enabled')
                ->setLabelAttribute('icon', 'check')
                ->setLabelAttribute('color', 'green')
            ;

            $menu->addChild('coupon_cancel', [ /* ... */ ]);
        }

    }


2. Register the menu event
--------------------------

After creating your menu event builder, your should register it in the symfony container via the ``config/services.yaml`` file.

.. code-block:: yaml

    # config/services.yml
    sylius.admin.menu_builder.coupon.show:
        class: App\Menu\CouponShowMenuBuilder
        arguments:
            - '@knp_menu.factory'
            - '@event_dispatcher'
        tags:
            - { name: knp_menu.menu_builder,  method: createMenu,  alias: sylius.admin.coupon.show }

3. Twig
-------

Here is a simple example of how to use your menu in Twig :

.. code-block:: jinja

    {% set menu = knp_menu_get('sylius.admin.coupon.show', [], {'coupon': coupon}) %}
    {{ knp_menu_render(menu, {'template': '@SyliusUi/Menu/top.html.twig'}) }}


.. _menus: https://docs.sylius.com/en/1.3/customization/menu.html
