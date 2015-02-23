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
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Report\Model\ReportInterface;

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
        $report = $this->findOr404($request);

        $formType = sprintf('sylius_data_fetcher_%s', $report->getDataFetcher());
        $configurationForm = $this->get('form.factory')->createNamed(
            'configuration',
            $formType,
            $report->getDataFetcherConfiguration()
        );

        if ($request->query->has('configuration')) {
            $configurationForm->submit($request);
        }

        return $this->render($this->config->getTemplate('show.html'), array(
            'report' => $report,
            'form' => $configurationForm->createView(),
            'configuration' => $configurationForm->getData()
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function embeddAction(Request $request, $report, array $configuration = array())
    {
        if (!$report instanceof ReportInterface) {
            $report = $this->get('sylius.repository.report')->findOneBy(array('code' => $report));
        }

        if (null === $report) {
            return $this->render('SyliusReportBundle::noDataTemplate.html.twig');
        }

        $configuration = $request->query->get('configuration', $configuration);
        $data = $this->get('sylius.report.data_fetcher')->fetch($report, $configuration);

        return $this->get('sylius.report.renderer')->render($report, $data);
    }
}
