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

namespace Sylius\Component\Grid\Definition;

class Field
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var string|null
     */
    private $sortable;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var int
     *
     * Position equals to 100 to ensure that wile sorting fields by position ASC
     * the fields positioned by default will be last
     */
    private $position = 100;

    private function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;

        $this->path = $name;
        $this->label = $name;
    }

    public static function fromNameAndType(string $name, string $type): self
    {
        return new self($name, $type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setSortable(?string $sortable): void
    {
        $this->sortable = $sortable;
    }

    public function getSortable(): ?string
    {
        return $this->sortable;
    }

    public function isSortable(): bool
    {
        return null !== $this->sortable;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
