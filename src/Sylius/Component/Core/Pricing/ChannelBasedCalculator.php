<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Pricing;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChannelBasedCalculator implements CalculatorInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * @param PriceableInterface $subject
     * @param array              $configuration
     * @param array              $context
     *
     * @return integer
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = array())
    {
        $currentChannel = $this->channelContext->getChannel();

        return $subject->getPricingConfiguration()[$currentChannel->getId()];
    }


    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::CHANNEL_BASED;
    }
}
