<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;

class ProductTranslation extends AbstractTranslation implements ProductTranslationInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $metaKeywords;

    /**
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }
}
