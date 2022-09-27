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

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultChannelsStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultPaymentMethodsStoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultPaymentMethodsStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_payment_methods(): void
    {
        /** @var DefaultPaymentMethodsStoryInterface $defaultPaymentMethodsStory */
        $defaultPaymentMethodsStory = self::getContainer()->get('sylius.data_fixtures.story.default_payment_methods');

        $defaultPaymentMethodsStory->build();

        $paymentMethod = $this->getPaymentMethodByCode('cash_on_delivery');
        $this->assertNotNull($paymentMethod, sprintf('Payment method "%s" was not found but it should.', 'cash_on_delivery'));
        $this->assertEquals('Cash on delivery', $paymentMethod->getName());
        $this->assertCount(1, $paymentMethod->getChannels());
        $this->assertEquals('FASHION_WEB', $paymentMethod->getChannels()->first()->getCode());

        $paymentMethod = $this->getPaymentMethodByCode('bank_transfer');
        $this->assertNotNull($paymentMethod, sprintf('Payment method "%s" was not found but it should.', 'bank_transfer'));
        $this->assertEquals('Bank transfer', $paymentMethod->getName());
        $this->assertCount(1, $paymentMethod->getChannels());
        $this->assertEquals('FASHION_WEB', $paymentMethod->getChannels()->first()->getCode());
        $this->assertTrue($paymentMethod->isEnabled());
    }

    private function getPaymentMethodByCode(string $code): ?PaymentMethodInterface
    {
        /** @var PaymentMethodRepositoryInterface $paymentMethodRepository */
        $paymentMethodRepository = self::getContainer()->get('sylius.repository.payment_method');

        return $paymentMethodRepository->findOneBy(['code' => $code]);
    }
}
