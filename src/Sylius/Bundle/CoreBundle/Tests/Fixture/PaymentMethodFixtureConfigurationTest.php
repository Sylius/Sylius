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
use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\PaymentMethodFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PaymentMethodFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function gateway_is_set_to_offline_by_default()
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['gateway' => 'offline'],
            'gateway'
        );
    }

    /**
     * @test
     */
    public function gateway_can_be_overwritten()
    {
        $this->assertProcessedConfigurationEquals(
            [['gateway' => 'custom']],
            ['gateway' => 'custom'],
            'gateway'
        );
    }

    /**
     * @test
     */
    public function payment_methods_must_be_set_and_not_empty()
    {
        $this->assertPartialConfigurationIsInvalid([[]], 'payment_methods');
        $this->assertPartialConfigurationIsInvalid([['payment_methods' => null]], 'payment_methods');
        $this->assertPartialConfigurationIsInvalid([['payment_methods' => []]], 'payment_methods');
    }

    /**
     * @test
     */
    public function if_payment_methods_contains_a_number_then_it_is_amount_of_randomly_generated_resources()
    {
        $processedConfiguration = (new PartialProcessor())->processConfiguration(
            $this->getConfiguration(),
            'payment_methods',
            [['payment_methods' => 3]]
        );

        $this->assertCount(3, $processedConfiguration['payment_methods']);

        $processedConfiguration = (new PartialProcessor())->processConfiguration(
            $this->getConfiguration(),
            'payment_methods',
            [['payment_methods' => '2']]
        );

        $this->assertCount(2, $processedConfiguration['payment_methods']);
    }

    /**
     * @test
     */
    public function payment_methods_can_be_populated_with_custom_names()
    {
        $this->assertProcessedConfigurationEquals(
            [['payment_methods' => ['PayPal', 'PayU']]],
            ['payment_methods' => ['PayPal', 'PayU']],
            'payment_methods'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new PaymentMethodFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
