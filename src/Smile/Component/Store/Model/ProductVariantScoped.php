<?php

namespace Smile\Component\Store\Model;


use Smile\Component\Scope\Entity\ScopedValueTrait;
use Smile\Component\Scope\ScopedValueInterface;

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