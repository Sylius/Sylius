<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Import\Writer\ORM;

use Sylius\Component\ImportExport\Writer\WriterInterface;

/**
 * Export reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
abstract class AbstractDoctrineWriter implements WriterInterface
{
    private $results;
    private $running = false;
    private $configuration;
    
    public function write(array $items)
    {   
        // foreach ($items as $item)
        // {            
            $item = $this->process($items);
        // }
        
        $results = array();

        $em->persist();
        $em->flush();
    }

    public function setConfiguration (array $configuration)
    {
        $this->configuration = $configuration;
    }  

    public abstract function process($result);
}