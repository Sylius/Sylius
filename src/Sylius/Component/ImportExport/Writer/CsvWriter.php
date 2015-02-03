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

use Doctrine\ORM\EntityManager;
use EasyCSV\Writer;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvWriter implements WriterInterface
{
    /**
     * Is EasySCV\Writer initialized
     *
     * @var boolean
     */
    private $running = false;

    /**
     * @var Writer
     */
    private $csvWriter;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @param array $items
     */
    public function write(array $items)
    {
        if (!$this->running) {
            $this->csvWriter = new Writer($this->configuration['file'], 'w');
            $this->csvWriter->setDelimiter($this->configuration['delimiter']);
            $this->csvWriter->setEnclosure($this->configuration['enclosure']);
            $this->running = true;
        }

        $this->csvWriter->writeRow($items);
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'csv';
    }
}