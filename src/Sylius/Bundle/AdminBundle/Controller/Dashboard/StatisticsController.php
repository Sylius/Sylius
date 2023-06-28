<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller\Dashboard;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class StatisticsController
{
    public function __construct(
        private Environment $templatingEngine,
        private StatisticsDataProviderInterface $statisticsDataProvider,
    ) {
    }

    public function renderStatistics(ChannelInterface $channel): Response
    {
        return new Response($this->templatingEngine->render(
            '@SyliusAdmin/Dashboard/Statistics/_template.html.twig',
            $this->statisticsDataProvider->getRawData(
                $channel,
                (new \DateTime('first day of january this year')),
                (new \DateTime('first day of january next year')),
                'month',
            ),
        ));
    }
}
