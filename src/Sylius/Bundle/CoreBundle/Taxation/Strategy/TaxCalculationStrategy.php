<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Taxation\Strategy;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class TaxCalculationStrategy implements TaxCalculationStrategyInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var OrderTaxesApplicatorInterface[]
     */
    private $applicators;

    /**
     * @param string $type
     * @param OrderTaxesApplicatorInterface[] $applicators
     */
    public function __construct($type, array $applicators)
    {
        $this->assertApplicatorsHaveCorrectType($applicators);

        $this->type = $type;
        $this->applicators = $applicators;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxes(OrderInterface $order, ZoneInterface $zone)
    {
        foreach ($this->applicators as $applicator) {
            $applicator->apply($order, $zone);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(OrderInterface $order, ZoneInterface $zone)
    {
        $channel = $order->getChannel();

        /** @var ChannelInterface $channel */
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $channel->getTaxCalculationStrategy() === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param OrderTaxesApplicatorInterface[] $applicators
     */
    private function assertApplicatorsHaveCorrectType(array $applicators)
    {
        Assert::allIsInstanceOf(
            $applicators,
            OrderTaxesApplicatorInterface::class,
            'Order taxes applicator should have type "%2$s". Got: %s'
        );
    }
}
