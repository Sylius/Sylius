<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardController
{
    /**
     * @var DashboardStatisticsProviderInterface
     */
    private $statisticsProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param DashboardStatisticsProviderInterface $statisticsProvider
     * @param EngineInterface $templatingEngine
     */
    public function __construct(DashboardStatisticsProviderInterface $statisticsProvider, EngineInterface $templatingEngine)
    {
        $this->statisticsProvider = $statisticsProvider;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $stats = $this->statisticsProvider->getStatistics();

        return $this->templatingEngine->renderResponse('SyliusAdminBundle:Dashboard:index.html.twig', ['statistics' => $stats]);
    }
}
