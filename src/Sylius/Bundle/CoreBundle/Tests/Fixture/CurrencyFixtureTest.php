<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\CurrencyFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CurrencyFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function currencies_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'currencies');
    }

    /**
     * @test
     */
    public function currencies_can_be_set()
    {
        $this->assertConfigurationIsValid([['currencies' => ['EUR', 'USD', 'PLN']]], 'currencies');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new CurrencyFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            'DEFAULT_CURRENCY_CODE'
        );
    }
}
