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

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\LocaleFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class LocaleFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function locales_are_not_required(): void
    {
        $this->assertConfigurationIsValid([[]], 'locales');
    }

    /**
     * @test
     */
    public function locales_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['locales' => ['en_US' => true, 'pl_PL' => false, 'es_ES' => true]]], 'locales');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): LocaleFixture
    {
        return new LocaleFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            'default_LOCALE'
        );
    }
}
