<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

class LoadReportsData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $reportRepository = $this->getReportRepository();

        $report = $reportRepository->createNew();
        $report->setName('Monthly orders (CSV)');
        $report->setFetcher($this->createFetcher(
            'order',
            array(
                'group' => 'm',
            )
        ));
        $report->setRenderer($this->createRenderer(
            'csv',
            array(
                'delimiter' => ';',
                'enclosure' => '"',
            )
        ));

        $manager->persist($report);

        $manager->flush();
    }

    private function createFetcher($type, array $configuration)
    {
        $fetcher = $this
            ->getReportDataFetcherRepository()
            ->createNew()
        ;

        $fetcher->setType($type);
        $fetcher->setConfiguration($configuration);

        return $fetcher;
    }

    private function createRenderer($type, array $configuration)
    {
        $renderer = $this
            ->getReportRendererRepository()
            ->createNew()
        ;

        $renderer->setType($type);
        $renderer->setConfiguration($configuration);

        return $renderer;
    }

    public function getOrder()
    {
        return 1;
    }
}
