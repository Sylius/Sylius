<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Variation\Model\Variant as BaseVariant;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Variant extends BaseVariant implements VariantInterface
{
    /**
     * @var \DateTime
     */
    protected $availableOn;

    /**
     * @var \DateTime
     */
    protected $availableUntil;

    public function __construct()
    {
        parent::__construct();

        $this->availableOn = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return parent::getObject();
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product = null)
    {
        return parent::setObject($product);
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return (new DateRange($this->availableOn, $this->availableUntil))->isInRange();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableOn()
    {
        return $this->availableOn;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableOn(\DateTime $availableOn = null)
    {
        $this->availableOn = $availableOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableUntil()
    {
        return $this->availableUntil;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableUntil(\DateTime $availableUntil = null)
    {
        $this->availableUntil = $availableUntil;
    }
}
