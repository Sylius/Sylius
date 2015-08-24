<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Reader;

use EasyCSV\Reader;
use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Reader\Factory\CsvReaderFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvReader implements ReaderInterface
{
    /**
     * @var boolean
     */
    private $running = false;
    /**
     * @var Reader
     */
    private $csvReader;
    /**
     * @var int
     */
    private $resultCode = 0;
    /**
     * @var CsvReaderFactoryInterface
     */
    private $csvReaderFactory;

    /**
     * @param CsvReaderFactoryInterface $csvReaderFactory
     */
    public function __construct(CsvReaderFactoryInterface $csvReaderFactory)
    {
        $this->csvReaderFactory = $csvReaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function read(array $configuration, LoggerInterface $logger)
    {
        if (!$this->running) {
            $this->csvReader = $this->csvReaderFactory->create($configuration);
            $this->running = true;
        }

        $data = array();

        for ($i = 0; $i < (int) $configuration['batch']; $i++) {
            $row = $this->csvReader->getRow();

            if (false === $row) {
                return empty($data) ? null : $data;
            }

            $data[] = $row;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job)
    {
        $job->addMetadata(array('result_code' => $this->resultCode));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'csv';
    }
}
