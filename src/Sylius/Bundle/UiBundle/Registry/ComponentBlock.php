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

final class ComponentBlock extends Block
{
    /**
     * @param array<string, mixed> $componentInputs
     * @param array<string, mixed>|null $context
     */
    public function __construct(
        string $name,
        string $eventName,
        private string $componentName,
        private array $componentInputs,
        ?array $context,
        ?int $priority,
        ?bool $enabled,
    ) {
        parent::__construct($name, $eventName, $context, $priority, $enabled);
    }

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    /** @return array<string> */
    public function getComponentInputs(): array
    {
        return $this->componentInputs;
    }

    public function overwriteWith(Block $block): self
    {
        if (!$block instanceof self) {
            throw new \DomainException(sprintf(
                'Trying to overwrite component block "%s" with block of different type "%s".',
                $this->name,
                get_class($block),
            ));
        }

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
            $block->getComponentName(),
            $block->getComponentInputs(),
            $block->context ?? $this->context,
            $block->priority ?? $this->priority,
            $block->enabled ?? $this->enabled,
        );
    }
}
