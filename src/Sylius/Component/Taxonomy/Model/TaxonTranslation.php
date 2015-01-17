<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Model;

use Sylius\Component\Translation\Model\AbstractTranslation;

/**
 * Model for taxon translations.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonTranslation extends AbstractTranslation implements TaxonTranslationInterface
{
    /**
     * Taxon id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Taxon name.
     *
     * @var string
     */
    protected $name;

    /**
     * Taxon slug.
     *
     * @var string
     */
    protected $slug;

    /**
     * Taxon permalink.
     *
     * @var string
     */
    protected $permalink;

    /**
     * Taxon description.
     *
     * @var string
     */
    protected $description;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
    }

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
    public function setSlug($slug = null)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;

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
}
