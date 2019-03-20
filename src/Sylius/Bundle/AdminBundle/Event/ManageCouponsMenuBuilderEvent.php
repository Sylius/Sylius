<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Stripe\Coupon;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class ManageCouponsMenuBuilderEvent extends MenuBuilderEvent
{
    /** @var Coupon */
    private $coupon;

    public function __construct(
        FactoryInterface $factory,
        ItemInterface $menu,
        Coupon $coupon
    ) {
        parent::__construct($factory, $menu);
        $this->coupon = $coupon;
    }

    public function getCoupon(): Coupon
    {
        return $this->coupon;
    }
}
