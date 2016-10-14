<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class Taxon extends BaseTaxon implements TaxonInterface
{
    use TimestampableTrait;

    /**
     * @var Collection|ProductInterface
     */
    protected $products;

    /**
     * @var Collection|ImageInterface[]
     */
    protected $images;

    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
        $this->products = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageByCode($code)
    {
        foreach ($this->images as $image) {
            if ($code === $image->getCode()) {
                return $image;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasImages()
    {
        return !$this->images->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(ImageInterface $image)
    {
        return $this->images->contains($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ImageInterface $image)
    {
        $image->setOwner($this);
        $this->images->add($image);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(ImageInterface $image)
    {
        if ($this->hasImage($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * {@inheritdoc}
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass()
    {
        return TaxonTranslation::class;
    }
}
