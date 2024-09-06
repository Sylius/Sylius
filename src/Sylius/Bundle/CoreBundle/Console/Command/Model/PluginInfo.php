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

namespace Sylius\Bundle\CoreBundle\Console\Command\Model;

trigger_deprecation(
    'sylius/core-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    PluginInfo::class,
);

final class PluginInfo
{
    public function __construct(private string $name, private string $description, private string $url)
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function url(): string
    {
        return $this->url;
    }
}
