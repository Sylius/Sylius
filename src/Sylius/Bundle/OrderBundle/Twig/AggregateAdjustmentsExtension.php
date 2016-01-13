<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Twig;

use Sylius\Bundle\OrderBundle\Aggregator\AdjustmentsAggregatorInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AggregateAdjustmentsExtension extends \Twig_Extension
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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_aggregate_adjustments', array($this, 'aggregateAdjustments'))
        );
    }

    /**
     * @param array $adjustments
     *
     * @return array
     */
    public function aggregateAdjustments(array $adjustments)
    {
        return $this->adjustmentsAggregator->aggregate($adjustments);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_aggregate_adjustments';
    }
}
