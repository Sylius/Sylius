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

namespace Sylius\Bundle\CoreBundle\Installer\Requirement;

final class Requirement
{
    public function __construct(private string $label, private bool $fulfilled, private bool $required = true, private ?string $help = null)
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isFulfilled(): bool
    {
        return $this->fulfilled;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }
}
