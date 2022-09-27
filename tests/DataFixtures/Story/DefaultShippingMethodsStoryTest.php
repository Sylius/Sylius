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

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultShippingMethodsStoryInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultShippingMethodsStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_shipping_methods(): void
    {
        /** @var DefaultShippingMethodsStoryInterface $defaultShippingMethodsStory */
        $defaultShippingMethodsStory = self::getContainer()->get('sylius.data_fixtures.story.default_shipping_methods');

        $defaultShippingMethodsStory->build();

        $shippingMethod = $this->getShippingMethodByCode('ups');
        $this->assertNotNull($shippingMethod, sprintf('Shipping method "%s" was not found but it should.', 'ups'));
        $this->assertEquals('UPS', $shippingMethod->getName());
        $this->assertCount(1, $shippingMethod->getChannels());
        $this->assertEquals('FASHION_WEB', $shippingMethod->getChannels()->first()->getCode());

        $shippingMethod = $this->getShippingMethodByCode('dhl_express');
        $this->assertNotNull($shippingMethod, sprintf('Shipping method "%s" was not found but it should.', 'dhl_express'));
        $this->assertEquals('DHL Express', $shippingMethod->getName());
        $this->assertCount(1, $shippingMethod->getChannels());
        $this->assertEquals('FASHION_WEB', $shippingMethod->getChannels()->first()->getCode());

        $shippingMethod = $this->getShippingMethodByCode('fedex');
        $this->assertNotNull($shippingMethod, sprintf('Shipping method "%s" was not found but it should.', 'fedex'));
        $this->assertEquals('FedEx', $shippingMethod->getName());
        $this->assertCount(1, $shippingMethod->getChannels());
        $this->assertEquals('FASHION_WEB', $shippingMethod->getChannels()->first()->getCode());
    }

    private function getShippingMethodByCode(string $code): ?ShippingMethodInterface
    {
        /** @var ShippingMethodRepositoryInterface $shippingMethodRepository */
        $shippingMethodRepository = self::getContainer()->get('sylius.repository.shipping_method');

        return $shippingMethodRepository->findOneBy(['code' => $code]);
    }
}
