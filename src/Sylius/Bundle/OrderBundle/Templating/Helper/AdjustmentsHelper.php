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

namespace Sylius\Bundle\OrderBundle\Templating\Helper;

use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\Templating\Helper\Helper;

class AdjustmentsHelper extends Helper
{
    /**
     * @var AdjustmentsAggregatorInterface
     */
    private $adjustmentsAggregator;

    /**
     * @param AdjustmentsAggregatorInterface $adjustmentsAggregator
     */
    public function __construct(AdjustmentsAggregatorInterface $adjustmentsAggregator)
    {
        $this->adjustmentsAggregator = $adjustmentsAggregator;
    }

    /**
     * @param iterable|AdjustmentInterface[] $adjustments
     *
     * @return array
     */
    public function getAggregatedAdjustments(iterable $adjustments): array
    {
        return $this->adjustmentsAggregator->aggregate($adjustments);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_adjustments';
    }
}
