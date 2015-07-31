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

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Writer\WriterInterface;

/**
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
abstract class AbstractDoctrineWriter implements WriterInterface
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var int
     */
    protected $resultCode;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->metadatas['row'] = 0;
        $this->resultCode = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $rawUsers, array $configuration, Logger $logger)
    {
        foreach ($rawUsers as $item) {
            try {
                $item = $this->process($item);
            } catch (Exception $e) {
                $this->logger->addError('Error occured during processing item. Error message: '.$e->getMessage());
                $this->resultCode = 1;
                $item = null;
            }
            if (!is_null($item)) {
                $this->entityManager->persist($item);
                $this->metadatas['row']++;
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Process an array and parse it into an object.
     *
     * @param array  $result
     * @param Logger $logger
     *
     * @return mixed
     */
    abstract protected function process(array $result, Logger $logger);

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job, array $config)
    {
        $job->addMetadata('result_code', $this->resultCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }
}
