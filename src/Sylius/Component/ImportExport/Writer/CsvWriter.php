<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Writer;

use EasyCSV\Writer;
use Gaufrette\Filesystem;
use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Writer\Factory\CsvWriterFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CsvWriter implements WriterInterface
{
    /**
     * @var boolean
     */
    private $running = false;
    /**
     * @var Writer
     */
    private $csvWriter;
    /**
     * @var boolean
     */
    private $isHeaderSet = false;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var int
     */
    private $resultCode = 0;
    /**
     * @var array
     */
    private $metadata = array();
    /**
     * @var CsvWriterFactoryInterface
     */
    private $csvWriterFactory;

    /**
     * @param Filesystem                $filesystem
     * @param CsvWriterFactoryInterface $csvWriterFactory
     */
    public function __construct(Filesystem $filesystem, CsvWriterFactoryInterface $csvWriterFactory)
    {
        $this->filesystem = $filesystem;
        $this->csvWriterFactory = $csvWriterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $rawUsers, array $configuration, LoggerInterface $logger)
    {
        if (!$this->running) {
            $this->csvWriter = $this->csvWriterFactory->create($configuration);
            $this->running = true;
            $this->metadata['row'] = 0;
        }

        if (!isset($rawUsers[0])) {
            return;
        }

        if (!$this->isHeaderSet) {
            $this->csvWriter->writeRow(array_keys($rawUsers[0]));
            $this->isHeaderSet = true;
        }

        $this->csvWriter->writeFromArray($rawUsers);
        $this->metadata['row'] += sizeof($rawUsers);
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job, array $configuration)
    {
        $this->metadata['file_path'] = $configuration['file'];
        $this->metadata['result_code'] = $this->resultCode;

        $job->addMetadata($this->metadata);
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
