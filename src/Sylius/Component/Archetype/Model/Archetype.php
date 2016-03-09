<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Archetype\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Variation\Model\OptionInterface;

/**
 * The archetype model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Archetype implements ArchetypeInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var Collection|AttributeInterface[]
     */
    protected $attributes;

    /**
     * @var Collection|OptionInterface[]
     */
    protected $options;

    /**
     * Parent archetype.
     *
     * @var ArchetypeInterface
     */
    protected $parent;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->attributes = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        if (!$this->hasParent()) {
            return $this->attributes;
        }

        return new ArrayCollection(array_merge($this->parent->getAttributes()->toArray(), $this->attributes->toArray()));
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(Collection $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttribute(AttributeInterface $attribute)
    {
        $this->attributes->add($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute(AttributeInterface $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(AttributeInterface $attribute)
    {
        return $this->attributes->contains($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (!$this->hasParent()) {
            return $this->options;
        }

        return new ArrayCollection(array_merge($this->parent->getOptions()->toArray(), $this->options->toArray()));
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(Collection $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(OptionInterface $option)
    {
        $this->options->add($option);
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption(OptionInterface $option)
    {
        $this->options->removeElement($option);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(OptionInterface $option)
    {
        return $this->options->contains($option);
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ArchetypeInterface $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->translate()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->translate()->setName($name);
    }
}
