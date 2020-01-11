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

namespace Sylius\Bundle\CoreBundle\Command\Model;

final class PluginInfo
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $url;

    public function __construct(string $name, string $description, string $url)
    {
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
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
