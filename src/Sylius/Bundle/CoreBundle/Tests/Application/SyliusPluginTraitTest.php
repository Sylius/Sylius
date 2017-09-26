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

namespace Sylius\Bundle\CoreBundle\Tests\Application;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusPluginTraitTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_crash(): void
    {
        new class() extends Bundle {
            use SyliusPluginTrait;
        };
    }
}
