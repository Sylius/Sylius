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
 * Model for product variants.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Variant extends BaseVariant implements VariantInterface
{
    /**
     * Available on.
     *
     * @var \DateTime
     */
    protected $availableOn;

    /**
     * Constructor.
     */
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
        return new \DateTime() >= $this->availableOn;
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
    public function setAvailableOn(\DateTime $availableOn)
    {
        $this->availableOn = $availableOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaults(BaseVariantInterface $masterVariant)
    {
        parent::setDefaults($masterVariant);

        if (!$masterVariant instanceof VariantInterface) {
            throw new \InvalidArgumentException('Product variants must implement "Sylius\Component\Product\Model\VariantInterface".');
        }

        $this->setAvailableOn($masterVariant->getAvailableOn());

        return $this;
    }
}
