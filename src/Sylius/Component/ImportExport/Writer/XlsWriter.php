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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class XlsWriter implements WriterInterface
{
    /**
     * @var array
     */
    private $configuration;

    public function write(array $items)
    {
        
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
        return 'xls';
    }
}