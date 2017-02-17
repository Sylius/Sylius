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
final class AggregateAdjustmentsExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_aggregate_adjustments', [AdjustmentsHelper::class, 'aggregateAdjustments']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_aggregate_adjustments';
    }
}
