<?php

namespace Smile\Component\Store\Model;


use Sylius\Component\Scope\Entity\ScopedValueTrait;
use Sylius\Component\Scope\ScopedValueInterface;

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