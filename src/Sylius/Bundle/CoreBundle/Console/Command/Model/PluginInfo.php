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

class_alias(PluginInfo::class, '\Sylius\Bundle\CoreBundle\Command\Model\PluginInfo');
