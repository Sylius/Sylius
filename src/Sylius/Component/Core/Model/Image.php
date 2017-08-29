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

namespace Sylius\Component\Core\Model;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
abstract class Image implements ImageInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var object
     */
    protected $owner;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): ?\SplFileInfo
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(?\SplFileInfo $file): void
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFile(): bool
    {
        return null !== $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPath(): bool
    {
        return null !== $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }
}
