<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\PricingBundle\Twig\PricingExtension as BasePricingExtension;
use Sylius\Bundle\PricingBundle\Templating\Helper\PricingHelper;
use Sylius\Component\Channel\Context\ChannelContext;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class PricingExtension extends BasePricingExtension
{
    /**
     * @var ChannelContext
     */
    protected $channelContext;

    /**
     * @param PricingHelper  $helper
     * @param ChannelContext $channelContext
     */
    public function __construct(PricingHelper $helper, ChannelContext $channelContext)
    {
        parent::__construct($helper);

        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(PriceableInterface $priceable, array $context = array())
    {
        return parent::calculatePrice($priceable, ['channel' => $this->channelContext->getChannel()] + $context);
    }
}
