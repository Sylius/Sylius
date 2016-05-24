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
     * @var ImageInterface
     */
    protected $image;

    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
        $this->products = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage()
    {
        return null !== $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(ImageInterface $image)
    {
        $this->image = $image;
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
