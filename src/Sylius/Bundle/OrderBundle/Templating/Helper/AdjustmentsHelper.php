<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Templating\Helper;

use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
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
     * @param array $adjustments
     *
     * @return array
     */
    public function getAggregatedAdjustments(array $adjustments)
    {
        return $this->adjustmentsAggregator->aggregate($adjustments);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_adjustments';
    }
}
