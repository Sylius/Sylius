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

use Monolog\Logger;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class XlsWriter implements WriterInterface
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * Work logger
     *
     * @var Logger
     */
    protected $logger;

    public function write(array $items)
    {
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'xls';
    }
}
