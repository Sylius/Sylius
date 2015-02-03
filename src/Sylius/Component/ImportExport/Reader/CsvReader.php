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

use Doctrine\ORM\EntityManager;
use EasyCSV\Reader;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvReader implements ReaderInterface
{
    /**
     * Is EasySCV\Reader initialized
     *
     * @var boolean
     */
    private $running = false;

    /**
     * @var Reader
     */
    private $csvReader;

    /**
     * @var array
     */
    private $configuration;

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->running) {
            $this->csvReader = new Reader($this->configuration['file'], 'r', $this->configuration["headers"]);
            $this->csvReader->setDelimiter($this->configuration['delimiter']);
            $this->csvReader->setEnclosure($this->configuration['enclosure']);
            $this->running = true;
        }
        
        return $this->csvReader->getRow();
    }

    /**
     * {@inheritdoc}
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