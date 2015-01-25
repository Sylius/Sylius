<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Store\Model;


use Sylius\Component\Scope\Entity\ScopedValueTrait;
use Sylius\Component\Scope\ScopedValueInterface;

/**
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
class ProductVariantScoped implements ScopedValueInterface
{
    use ScopedValueTrait;

    /**
     * @var int
     */
    private $price;

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = (int)$price;

        return $this;
    }
}