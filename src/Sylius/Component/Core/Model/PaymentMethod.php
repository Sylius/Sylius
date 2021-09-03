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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;
use Sylius\Component\Payment\Model\PaymentMethodTranslation;

class PaymentMethod extends BasePaymentMethod implements PaymentMethodInterface
{
    /**
     * @var Collection|BaseChannelInterface[]
     *
     * @psalm-var Collection<array-key, BaseChannelInterface>
     */
    protected Collection $channels;

    protected ?GatewayConfigInterface $gatewayConfig = null;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, BaseChannelInterface> $this->channels */
        $this->channels = new ArrayCollection();
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function setGatewayConfig(?GatewayConfigInterface $gatewayConfig): void
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    public function getGatewayConfig(): ?GatewayConfigInterface
    {
        return $this->gatewayConfig;
    }

    public static function getTranslationClass(): string
    {
        return PaymentMethodTranslation::class;
    }
}
