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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;

final class PaymentMethodTest extends TestCase
{
    private ChannelInterface&MockObject $channel;

    private PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->paymentMethod = new PaymentMethod();
    }

    public function testShouldExtendBasePaymentMethod(): void
    {
        $this->assertInstanceOf(BasePaymentMethod::class, $this->paymentMethod);
    }

    public function testShouldImplementPaymentMethodInterface(): void
    {
        $this->assertInstanceOf(PaymentMethodInterface::class, $this->paymentMethod);
    }

    public function testShouldInitializeChannelsCollection(): void
    {
        $this->assertInstanceOf(Collection::class, $this->paymentMethod->getChannels());
    }

    public function testShouldChannelsCollectionBeEmptyByDefault(): void
    {
        $this->assertTrue($this->paymentMethod->getChannels()->isEmpty());
    }

    public function testShouldAddChannel(): void
    {
        $this->paymentMethod->addChannel($this->channel);

        $this->assertTrue($this->paymentMethod->hasChannel($this->channel));
    }

    public function testShouldRemoveChannel(): void
    {
        $this->paymentMethod->addChannel($this->channel);

        $this->paymentMethod->removeChannel($this->channel);

        $this->assertFalse($this->paymentMethod->hasChannel($this->channel));
    }
}
