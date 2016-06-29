<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Shipping\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Shipping\Model\ShippingMethodTranslation;
use Sylius\Taxation\Model\TaxCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
{
    /**
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * @var TaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(ZoneInterface $zone)
    {
        $this->zone = $zone;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass()
    {
        return ShippingMethodTranslation::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory()
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(TaxCategoryInterface $category = null)
    {
        $this->taxCategory = $category;
    }
}
