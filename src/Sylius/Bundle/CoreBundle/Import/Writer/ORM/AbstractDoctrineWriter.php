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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
abstract class AbstractDoctrineWriter implements WriterInterface
{
    protected $configuration;
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
    protected $resultCode;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->metadatas['row'] = 0;
        $this->resultCode = 0;
    }

    public function write(array $items)
    {
        foreach ($items as $item) {
            try {
                $item = $this->process($item);
            } catch (Exception $e) {
                $this->logger->addError('Error occured during processing item. Error message: '.$e->getMessage());
                $this->resultCode = 1;
                $item = null;
            }
            if (!is_null($item)) {
                $this->em->persist($item);
                $this->metadatas['row']++;
            }
        }

        $this->em->flush();
    }

    public function setConfiguration(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;

        $this->logger = $logger;
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
