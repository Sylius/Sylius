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
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Taxonomy\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;

class Taxon extends BaseTaxon implements TaxonInterface
{
    use TimestampableTrait;

    /**
     * @var ArrayCollection
     */
    protected $products;

    /**
     * @var ArrayCollection
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
    public function hasImages()
    {
        return !$this->images->isEmpty();
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
            if($image->getCode() === $code) {
                return $image;
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(TaxonImageInterface $image)
    {
        $image->setTaxon($this);
        $this->images->add($image);
    }

    public function removeImage(TaxonImageInterface $image)
    {
        if($this->images->contains($image)) {
            $image->setTaxon(null);
            $this->images->remove($image);
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
