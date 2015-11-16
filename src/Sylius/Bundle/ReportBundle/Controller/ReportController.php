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
use Sylius\Component\Report\DataFetcher\DelegatingDataFetcherInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\Renderer\DelegatingRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
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
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $report = $this->findOr404($configuration);

        $formType = sprintf('sylius_data_fetcher_%s', $report->getDataFetcher());
        $configurationForm = $this->container->get('form.factory')->createNamed(
            'configuration',
            $formType,
            $report->getDataFetcherConfiguration()
        );

        if ($request->query->has('configuration')) {
            $configurationForm->submit($request);
        }

        return $this->container->get('templating')->renderResponse($configuration->getTemplate('show.html'), array(
            'report'        => $report,
            'form'          => $configurationForm->createView(),
            'configuration' => $configurationForm->getData(),
        ));
    }

    /**
     * @param Request $request
     * @param string  $report
     * @param array   $configuration
     *
     * @return Response
     */
    public function embedAction(Request $request, $report, array $configuration = array())
    {
        if (!$report instanceof ReportInterface) {
            $report = $this->repository->findOneBy(array('code' => $report));
        }

        if (null === $report) {
            return $this->container->get('templating')->renderResponse('SyliusReportBundle::noDataTemplate.html.twig');
        }

        $configuration = $request->query->get('configuration', $configuration);
        $data = $this->getReportDataFetcher()->fetch($report, $configuration);

        return new Response($this->getReportRenderer()->render($report, $data));
    }

    /**
     * @return DelegatingRendererInterface
     */
    private function getReportRenderer()
    {
        return $this->container->get('sylius.report.renderer');
    }

    /**
     * @return DelegatingDataFetcherInterface
     */
    private function getReportDataFetcher()
    {
        return $this->container->get('sylius.report.data_fetcher');
    }
}
