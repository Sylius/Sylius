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

use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface as BaseTaxonInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ProductTaxon implements ProductTaxonInterface, TaxonInterface
{
    /**
     * @var mixed
     */
    protected $id;
    
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var TaxonInterface
     */
    protected $taxon;

    /**
     * @var int
     */
    protected $position;
 
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxon()
    {
        return $this->taxon;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxon(TaxonInterface $taxon)
    {
        $this->taxon = $taxon;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->taxon->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->taxon->setCode($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->taxon->getImages();
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesByType($type)
    {
        return $this->taxon->getImagesByType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function hasImages()
    {
        return $this->taxon->hasImages();
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(ImageInterface $image)
    {
        return $this->taxon->hasImage($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(ImageInterface $image)
    {
        $this->taxon->addImage($image);
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(ImageInterface $image)
    {
        $this->taxon->removeImage($image);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->taxon->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug = null)
    {
        $this->taxon->setSlug($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return $this->taxon->isRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        return $this->taxon->getRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->taxon->getParent();
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(BaseTaxonInterface $taxon = null)
    {
        $this->taxon->setParent($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function getParents()
    {
        return $this->taxon->getParents();
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->taxon->getChildren();
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(BaseTaxonInterface $taxon)
    {
        return $this->taxon->hasChild($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(BaseTaxonInterface $taxon)
    {
        $this->taxon->addChild($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(BaseTaxonInterface $taxon)
    {
        $this->taxon->removeChild($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeft()
    {
        return $this->taxon->getLeft();
    }

    /**
     * {@inheritdoc}
     */
    public function setLeft($left)
    {
        $this->taxon->setLeft($left);
    }

    /**
     * {@inheritdoc}
     */
    public function getRight()
    {
        return $this->taxon->getRight();
    }

    /**
     * {@inheritdoc}
     */
    public function setRight($right)
    {
        $this->taxon->setRight($right);
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->taxon->getLevel();
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($level)
    {
        $this->taxon->setLevel($level);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->taxon->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->taxon->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->taxon->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->taxon->setDescription($description);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslations()
    {
        return $this->taxon->getTranslations();
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslation($locale = null)
    {
        return $this->taxon->getTranslation($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTranslation(TranslationInterface $translation)
    {
        return $this->taxon->hasTranslation($translation);
    }

    /**
     * {@inheritdoc}
     */
    public function addTranslation(TranslationInterface $translation)
    {
        $this->taxon->addTranslation($translation);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        $this->taxon->removeTranslation($translation);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale($locale)
    {
        $this->taxon->setCurrentLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function setFallbackLocale($locale)
    {
        $this->taxon->setFallbackLocale($locale);
    }
}
