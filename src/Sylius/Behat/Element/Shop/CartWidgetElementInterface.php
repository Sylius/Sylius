<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Shop;

interface CartWidgetElementInterface
{
    public function getCartTotalQuantity(): int;
}
