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
use Sylius\Bundle\CoreBundle\Fixture\CurrencyFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CurrencyFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function currencies_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'currencies');
    }

    /**
     * @test
     */
    public function currencies_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['currencies' => ['EUR', 'USD', 'PLN']]], 'currencies');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): CurrencyFixture
    {
        return new CurrencyFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock()
        );
    }
}
