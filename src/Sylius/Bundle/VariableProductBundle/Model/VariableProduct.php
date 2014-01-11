<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ProductBundle\Model\Product;

/**
 * Default model implementation of VariableProductInterface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariableProduct extends Product implements VariableProductInterface
{
    /**
     * Product variants.
     *
     * @var VariantInterface[]
     */
    protected $variants;

    /**
     * Product options.
     *
     * @var OptionInterface[]
     */
    protected $options;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->variants = new ArrayCollection();
        $this->options = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return $this
            ->getMasterVariant()
            ->isAvailable()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableOn()
    {
        return $this
            ->getMasterVariant()
            ->getAvailableOn()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableOn(\DateTime $availableOn)
    {
        $this
            ->getMasterVariant()
            ->setAvailableOn($availableOn)
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMasterVariant()
    {
        foreach ($this->variants as $variant) {
            if ($variant->isMaster()) {
                return $variant;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMasterVariant(VariantInterface $masterVariant)
    {
        if ($this->variants->contains($masterVariant)) {
            return $this;
        }

        $masterVariant->setProduct($this);
        $masterVariant->setMaster(true);

        $this->variants->add($masterVariant);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariants()
    {
        return !$this->getVariants()->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariants()
    {
        return $this->variants->filter(function (VariantInterface $variant) {
            return !$variant->isMaster();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableVariants()
    {
        return $this->variants->filter(function (VariantInterface $variant) {
            return !$variant->isMaster() && $variant->isAvailable();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setVariants(Collection $variants)
    {
        $this->variants->clear();

        foreach ($variants as $variant) {
            $this->addVariant($variant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addVariant(VariantInterface $variant)
    {
        if (!$this->hasVariant($variant)) {
            $variant->setProduct($this);
            $this->variants->add($variant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariant(VariantInterface $variant)
    {
        if ($this->hasVariant($variant)) {
            $variant->setProduct(null);
            $this->variants->removeElement($variant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariant(VariantInterface $variant)
    {
        return $this->variants->contains($variant);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOptions()
    {
        return !$this->options->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(Collection $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(OptionInterface $option)
    {
        if (!$this->hasOption($option)) {
            $this->options->add($option);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption(OptionInterface $option)
    {
        if ($this->hasOption($option)) {
            $this->options->removeElement($option);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(OptionInterface $option)
    {
        return $this->options->contains($option);
    }
}
