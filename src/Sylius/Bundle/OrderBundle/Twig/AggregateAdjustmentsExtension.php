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

use Sylius\Bundle\OrderBundle\Templating\Helper\AdjustmentsHelper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AggregateAdjustmentsExtension extends \Twig_Extension
{
    /**
     * @var AdjustmentsHelper
     */
    private $adjustmentsHelper;

    /**
     * @param AdjustmentsHelper $adjustmentsHelper
     */
    public function __construct(AdjustmentsHelper $adjustmentsHelper)
    {
        $this->adjustmentsHelper = $adjustmentsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_aggregate_adjustments', [$this, 'aggregateAdjustments']),
        ];
    }

    /**
     * @param array $adjustments
     *
     * @return array
     */
    public function aggregateAdjustments(array $adjustments)
    {
        return $this->adjustmentsHelper->getAggregatedAdjustments($adjustments);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_aggregate_adjustments';
    }
}
