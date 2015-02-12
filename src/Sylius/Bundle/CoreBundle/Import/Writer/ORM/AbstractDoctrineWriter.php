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

use Doctrine\ORM\EntityManager;
use Monolog\Logger;

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
    private $em;

    /**
     * Work logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @var int
     */
    private $resultCode = 0;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function write(array $items)
    {
        foreach ($items as $item) {
            $item = $this->process($item);
            $this->em->persist($item);
        }

        $this->em->flush();
    }

    public function setConfiguration(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
    }  

    public abstract function process($result);

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job)
    {
        $job->addMetadata('result_code',$this->resultCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }
}
