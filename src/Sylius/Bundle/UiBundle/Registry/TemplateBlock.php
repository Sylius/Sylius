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

namespace Sylius\Bundle\UiBundle\Registry;

final class TemplateBlock
{
    /** @var string */
    private $name;

    /** @var string */
    private $template;

    /** @var array */
    private $context;

    /** @var int */
    private $priority;

    /** @var bool */
    private $enabled;

    public function __construct(
        string $name,
        string $template,
        array $context,
        int $priority,
        bool $enabled
    ) {
        $this->name = $name;
        $this->template = $template;
        $this->context = $context;
        $this->priority = $priority;
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
