<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;
use Sylius\Component\Payment\Model\PaymentMethodTranslation;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentMethod extends BasePaymentMethod implements PaymentMethodInterface
{
    /**
     * @var Collection
     */
    protected $channels;

    /**
     * @var GatewayConfigInterface
     */
    protected $gatewayConfig;

    public function __construct()
    {
        parent::__construct();

        $this->channels = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(BaseChannelInterface $channel)
    {
        return $this->channels->contains($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(BaseChannelInterface $channel)
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(BaseChannelInterface $channel)
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setGatewayConfig(GatewayConfigInterface $gatewayConfig)
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @return GatewayConfigInterface
     */
    public function getGatewayConfig()
    {
        return $this->gatewayConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getGateway()
    {
        if (null !== $this->gatewayConfig) {
            return $this->gatewayConfig->getGatewayName();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass()
    {
        return PaymentMethodTranslation::class;
    }
}
