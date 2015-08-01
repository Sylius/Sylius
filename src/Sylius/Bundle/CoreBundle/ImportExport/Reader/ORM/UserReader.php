<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\ImportExport\Reader\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\ImportExport\Processor\UserProcessorInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReader implements ReaderInterface
{
    /**
     * @var EntityRepository
     */
    private $userRepository;

    /**
     * @var UserProcessorInterface
     */
    private $userProcessor;

    /**
     * @var array
     */
    private $metadata = array();

    /**
     * @param UserProcessorInterface $userProcessor
     * @param EntityRepository       $userRepository
     */
    public function __construct(
        UserProcessorInterface $userProcessor,
        EntityRepository $userRepository
    ) {
        $this->userProcessor = $userProcessor;
        $this->userRepository = $userRepository;
        $this->metadata['result_code'] = 0;
        $this->metadata['offset'] = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function read(array $configuration, LoggerInterface $logger)
    {
        $read = $this->readFromRepository($configuration);
        $this->metadata['offset'] += $configuration['batch_size'];

        if (empty($read)) {
            return;
        }

        return $this->userProcessor->convert($read, $configuration['date_format']);
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job)
    {
        $job->addMetadata($this->metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->metadata['result_code'];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'user_orm';
    }

    /**
     * @param array $configuration
     *
     * @return array
     */
    private function readFromRepository(array $configuration)
    {
        return $this->userRepository->createQueryBuilder('user')
            ->addSelect('customer')
            ->leftJoin('user.customer', 'customer')
            ->setFirstResult($this->metadata['offset'])
            ->setMaxResults($configuration['batch_size'])
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
}
