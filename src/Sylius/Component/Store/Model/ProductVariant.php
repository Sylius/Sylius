<?php

namespace Sylius\Component\Store\Model;

use Sylius\Component\Scope\Entity\ScopeAwareTrait;
use Sylius\Component\Scope\ScopeAwareInterface;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

class ProductVariant extends BaseProductVariant implements ScopeAwareInterface
{
    use ScopeAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->initScopeAware();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        /** @var ProductVariantScoped $scopedValue */
        try {
            $scopedValue = $this->scope();
        } catch (\RuntimeException $e) {
            // Not in store context, return fallback value
            return $this->price;
        }

        if (!$scopedValue->getId()) {
            // New value, set fallback price
            $scopedValue->setPrice($this->price);
        }

        return $scopedValue->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        try {
            $this->scope()->setPrice($price);
        } catch (\RuntimeException $e) {
            // Not in store context, set default value
            $this->price = (int)$price;
        }

        return $this;
    }
}