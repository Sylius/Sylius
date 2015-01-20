<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReportController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request)
    {
        $report = $this->findOr404($request);

        $dataFetcher = $this->getDataFetcherRegistry()->get($report->getDataFetcher());
        $data = $dataFetcher->fetch($report->getDataFetcherConfiguration());

        $renderer = $this->getRendererRegistry()->get($report->getRenderer());
        $rendererConfiguration = $report->getRendererConfiguration();

        $reportData = array("report" => $report, 'data' => $data);

        return $renderer->render($reportData, $rendererConfiguration);
    }

    private function getDataFetcherRegistry()
    {
        return $this->get('sylius.registry.report.data_fetcher');
    }

    private function getRendererRegistry()
    {
        return $this->get('sylius.registry.report.renderer');
    }
}
