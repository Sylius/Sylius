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
final class PaymentMethodFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_has_offline_gateway_by_default()
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
    public function its_default_gateway_can_be_overwritten()
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
    public function it_requires_payment_methods_node_to_be_set()
    {
        $this->assertPartialConfigurationIsInvalid([[]], 'payment_methods');
        $this->assertPartialConfigurationIsInvalid([['payment_methods' => null]], 'payment_methods');
        $this->assertPartialConfigurationIsInvalid([['payment_methods' => []]], 'payment_methods');
    }

    /**
     * @test
     */
    public function its_payment_methods_can_be_set()
    {
        $this->assertProcessedConfigurationEquals(
            [['payment_methods' => ['PayPal', 'PayU']]],
            ['payment_methods' => ['PayPal', 'PayU']],
            'payment_methods'
        );
    }

    /**
     * @test
     */
    public function it_generates_random_payment_methods_names_if_number_is_given()
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
