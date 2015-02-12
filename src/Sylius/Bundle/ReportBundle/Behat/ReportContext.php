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
 * ReportContext for ReportBundle scenarios
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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
        $repository = $this->getRepository('report');

        foreach ($table->getHash() as $data) {
            $this->thereIsReport($data['name'], $data['description'], $data["code"], $data['renderer'], $data["renderer_configuration"], $data["data_fetcher"], $data["data_fetcher_configuration"], false);
        }

        $manager->flush();
    }
    
    public function thereIsReport($name, $description, $code, $rendererType, $rendererConfiguration, $dataFetcherType, $dataFetcherConfiguration, $flush = true)
    {
        $repository = $this->getRepository('report');

        $report = $repository->createNew();
        $report->setName($name);
        $report->setDescription($description);
        $report->setCode($code);
        
        $report->setRenderer($rendererType);
        $report->setRendererConfiguration($this->getConfiguration($rendererConfiguration));

        $dataFetcherConfiguration = $this->getConfiguration($dataFetcherConfiguration);
        $dataFetcherConfiguration["start"] = new \DateTime($dataFetcherConfiguration["start"]);
        $dataFetcherConfiguration["end"] = new \DateTime($dataFetcherConfiguration["end"]);
        $dataFetcherConfiguration["empty_records"] = isset($dataFetcherConfiguration["empty_records"]) ? false : true;

        $report->setDataFetcher($dataFetcherType);
        $report->setDataFetcherConfiguration($dataFetcherConfiguration);

        $menager = $this->getEntityManager();
        $menager->persist($report);

        if ($flush) {
            $menager->flush();
        }

        return $report;
    }
}