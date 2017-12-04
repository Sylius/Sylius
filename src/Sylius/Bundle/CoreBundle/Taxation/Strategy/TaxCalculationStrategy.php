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

namespace Sylius\Bundle\CoreBundle\Taxation\Strategy;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Webmozart\Assert\Assert;

final class TaxCalculationStrategy implements TaxCalculationStrategyInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array|OrderTaxesApplicatorInterface[]
     */
    private $applicators;

    /**
     * @param string $type
     * @param array|OrderTaxesApplicatorInterface[] $applicators
     */
    public function __construct(string $type, array $applicators)
    {
        $this->assertApplicatorsHaveCorrectType($applicators);

        $this->type = $type;
        $this->applicators = $applicators;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxes(OrderInterface $order, ZoneInterface $zone): void
    {
        foreach ($this->applicators as $applicator) {
            $applicator->apply($order, $zone);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function supports(OrderInterface $order, ZoneInterface $zone): bool
    {
        $channel = $order->getChannel();

        /** @var ChannelInterface $channel */
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $channel->getTaxCalculationStrategy() === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param array|OrderTaxesApplicatorInterface[] $applicators
     *
     * @throws \InvalidArgumentException
     */
    private function assertApplicatorsHaveCorrectType(array $applicators): void
    {
        Assert::allIsInstanceOf(
            $applicators,
            OrderTaxesApplicatorInterface::class,
            'Order taxes applicator should have type "%2$s". Got: %s'
        );
    }
}
