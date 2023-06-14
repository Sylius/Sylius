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

final class TemplateBlock
{
    public function __construct(
        private string $name,
        private string $eventName,
        private ?string $template,
        private ?array $context,
        private ?int $priority,
        private ?bool $enabled,
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

    public function getTemplate(): string
    {
        if ($this->template === null) {
            throw new \DomainException(sprintf(
                'There is no template defined for block "%s" in event "%s".',
                $this->name,
                $this->eventName,
            ));
        }

        return $this->template;
    }

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

    public function overwriteWith(self $block): self
    {
        if ($this->name !== $block->name) {
            throw new \DomainException(sprintf(
                'Trying to overwrite block "%s" with block "%s".',
                $this->name,
                $block->name,
            ));
        }

        return new self(
            $this->name,
            $block->eventName,
            $block->template ?? $this->template,
            $block->context ?? $this->context,
            $block->priority ?? $this->priority,
            $block->enabled ?? $this->enabled,
        );
    }
}
