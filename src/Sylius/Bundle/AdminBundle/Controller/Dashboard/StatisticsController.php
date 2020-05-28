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

namespace Sylius\Bundle\AdminBundle\Controller\Dashboard;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

final class StatisticsController
{
    /** @var EngineInterface */
    private $templatingEngine;

    /** @var StatisticsDataProviderInterface */
    private $statisticsDataProvider;

    public function __construct(
        EngineInterface $templatingEngine,
        StatisticsDataProviderInterface $statisticsDataProvider
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->statisticsDataProvider = $statisticsDataProvider;
    }

    public function renderStatistics(ChannelInterface $channel): Response
    {
        return $this->templatingEngine->renderResponse(
            '@SyliusAdmin/Dashboard/Statistics/_template.html.twig',
            $this->statisticsDataProvider->getRawData(
                $channel,
                (new \DateTime('first day of january this year')),
                (new \DateTime('first day of january next year')),
                'month'
            )
        );
    }
}
