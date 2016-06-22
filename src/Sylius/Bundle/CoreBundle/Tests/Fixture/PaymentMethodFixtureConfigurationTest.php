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
    public function payment_methods_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'payment_methods');
    }

    /**
     * @test
     */
    public function payment_methods_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function payment_methods_can_be_populated_with_custom_names()
    {
        $this->assertProcessedConfigurationEquals(
            [['payment_methods' => ['PayPal', 'PayU']]],
            ['payment_methods' => [['name' => 'PayPal'], ['name' => 'PayU']]],
            'payment_methods'
        );
    }

    /**
     * @test
     */
    public function payment_method_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['payment_methods' => [null]]], 'payment_methods');
        $this->assertPartialConfigurationIsInvalid([['payment_methods' => [['name' => null]]]], 'payment_methods');

        $this->assertConfigurationIsValid([['payment_methods' => [['name' => 'custom1']]]], 'payment_methods');
    }

    /**
     * @test
     */
    public function payment_method_code_is_optional()
    {
        $this->assertConfigurationIsValid([['payment_methods' => [['code' => 'CUSTOM']]]], 'payment_methods.*.code');
    }

    /**
     * @test
     */
    public function payment_method_gateway_is_optional()
    {
        $this->assertConfigurationIsValid([['payment_methods' => [['gateway' => 'online']]]], 'payment_methods.*.gateway');
    }

    /**
     * @test
     */
    public function payment_method_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['payment_methods' => [['enabled' => false]]]], 'payment_methods.*.enabled');
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
