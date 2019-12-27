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

    /** @var int */
    private $priority;

    /** @var bool */
    private $enabled;

    public function __construct(
        string $name,
        string $template,
        int $priority,
        bool $enabled
    ) {
        $this->name = $name;
        $this->template = $template;
        $this->priority = $priority;
        $this->enabled = $enabled;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function template(): string
    {
        return $this->template;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }
}
