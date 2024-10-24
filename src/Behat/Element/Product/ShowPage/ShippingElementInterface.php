<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;

interface ShippingElementInterface
{
    public function getProductShippingCategory(): string;

    public function getProductHeight(): float;

    public function getProductDepth(): float;

    public function getProductWeight(): float;

    public function getProductWidth(): float;
}
