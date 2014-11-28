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

use Sylius\Component\Translation\Model\AbstractTranslation;

/**
 * Sylius catalog product translation model.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductTranslation extends AbstractTranslation implements ProductTranslationInterface
{
    /**
     * Product id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Product name.
     *
     * @var string
     */
    protected $name;

    /**
     * Permalink for the product.
     * Used in url to access it.
     *
     * @var string
     */
    protected $slug;

    /**
     * Product description.
     *
     * @var string
     */
    protected $description;

    /**
     * Meta keywords.
     *
     * @var string
     */
    protected $metaKeywords;

    /**
     * Meta description.
     *
     * @var string
     */
    protected $metaDescription;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
