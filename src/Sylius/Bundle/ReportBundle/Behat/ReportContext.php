<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

/**
 * ReportContext for ReportBundle scenarios.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 */
class ReportContext extends DefaultContext
{
    /**
     * @Given there are following reports configured:
     * @And there are following reports configured:
     */
    public function thereAreReports(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $this->thereIsReport(
                $data['name'],
                $data['description'],
                $data['code'],
                $data['renderer'],
                $data['renderer_configuration'],
                $data['data_fetcher'],
                $data['data_fetcher_configuration'],
                false
            );
        }

        $manager->flush();
    }

    /**
     * Create a report.
     *
     * @param string $name
     * @param string $description
     * @param string $code
     * @param string $rendererType
     * @param mixed  $rendererConfiguration
     * @param string $dataFetcherType
     * @param mixed  $dataFetcherConfiguration
     * @param bool   $flush
     *
     * @return mixed
     */
    public function thereIsReport(
        $name,
        $description,
        $code,
        $rendererType,
        $rendererConfiguration,
        $dataFetcherType,
        $dataFetcherConfiguration,
        $flush = true
    ) {
        $factory = $this->getFactory('report');

        $report = $factory->createNew();
        $report->setName($name);
        $report->setDescription($description);
        $report->setCode($code);

        $report->setRenderer($rendererType);
        $report->setRendererConfiguration($this->getConfiguration($rendererConfiguration));

        $dataFetcherConfiguration = $this->getConfiguration($dataFetcherConfiguration);
        $dataFetcherConfiguration['start'] = new \DateTime($dataFetcherConfiguration['start']);
        $dataFetcherConfiguration['end'] = new \DateTime($dataFetcherConfiguration['end']);
        $dataFetcherConfiguration['empty_records'] = isset($dataFetcherConfiguration['empty_records']) ? false : true;

        $report->setDataFetcher($dataFetcherType);
        $report->setDataFetcherConfiguration($dataFetcherConfiguration);

        $manager = $this->getEntityManager();
        $manager->persist($report);

        if ($flush) {
            $manager->flush();
        }

        return $report;
    }

    /**
     * @Then the report row for date :date will have a total amount of :total
     */
    public function theReportRowForDateWillHaveATotalAmountOf($date, $total)
    {
        $page = $this->getSession()->getPage();
        $rows = $page->findAll('css', 'table tbody tr');
        foreach ($rows as $row) {
            $columns = $row->findAll('css', 'td');
            if ($columns[1]->getText() === $date) {
                \PHPUnit_Framework_Assert::assertEquals($columns[2]->getText(), $total);
            }
        }
    }

    /**
     * @Given order #:orderNumber will be completed on :date
     */
    public function orderWillBeCompletedOn($orderNumber, $date)
    {
        $orderRepository = $this->getContainer()->get('sylius.repository.cart');
        $orderManager = $this->getContainer()->get('sylius.manager.cart');

        $order = $orderRepository->findOneBy(['number' => $orderNumber]);
        $order->setCompletedAt(new \DateTime($date));

        $orderManager->flush();
    }
}
