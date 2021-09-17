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

namespace Sylius\Bundle\ApiBundle\Application\Command;

final class FooCommand
{
    private string $bar;

    public function __construct(string $bar)
    {
        $this->bar = $bar;
    }
}
