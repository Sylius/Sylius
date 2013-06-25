<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Report\DataFetcher;

use Sylius\Bundle\ReportBundle\DataFetcher\OrmDataFetcher;
use YaLinqo\Enumerable;

class OrderDataFetcher extends OrmDataFetcher
{
    public function fetch(array $configuration)
    {
        return Enumerable::from($this->findAll())
            ->groupBy(function($order) use ($configuration) {
                return $order->getCreatedAt()->format($configuration['group']);
            }, null, function($orders) use ($configuration) {
                return array($orders[0]->getCreatedAt()->format($configuration['group']), Enumerable::from($orders)->count());
            })
            ->toValues()
            ->toArray()
        ;
    }

    public function getConfigurationFormType()
    {
        return 'sylius_report_data_fetcher_order_configuration';
    }
}
