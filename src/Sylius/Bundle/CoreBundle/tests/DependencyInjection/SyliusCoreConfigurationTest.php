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

namespace Tests\Sylius\Bundle\CoreBundle\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Configuration;

final class SyliusCoreConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    #[Test]
    public function it_sets_default_filesystem_adapter(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['filesystem' => ['adapter' => 'default']],
            'filesystem',
        );
    }

    #[Test]
    public function it_allows_to_define_filesystem_adapter(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['filesystem' => ['adapter' => 'default']]],
            ['filesystem' => ['adapter' => 'default']],
            'filesystem',
        );

        $this->assertProcessedConfigurationEquals(
            [['filesystem' => ['adapter' => 'flysystem']]],
            ['filesystem' => ['adapter' => 'flysystem']],
            'filesystem',
        );
    }

    #[Test]
    public function it_does_not_allow_to_define_invalid_filesystem_adapter(): void
    {
        $this->assertConfigurationIsInvalid(
            [['filesystem' => ['adapter' => 'yolo']]],
            'Expected adapter "default" or "flysystem", but "yolo" passed.',
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
