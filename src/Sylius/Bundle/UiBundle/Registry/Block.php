<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Registry;

abstract class Block
{
    /**
     * @param array<string, mixed>|null $context
     */
    public function __construct(
        protected string $name,
        protected string $eventName,
        protected ?array $context,
        protected ?int $priority,
        protected ?bool $enabled,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context ?? [];
    }

    public function getPriority(): int
    {
        return $this->priority ?? 0;
    }

    public function isEnabled(): bool
    {
        return $this->enabled ?? true;
    }

    abstract public function overwriteWith(self $block): self;
}
