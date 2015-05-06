<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\Export\Reader\ORM\Processor\UserProcessorInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReader implements ReaderInterface
{
    /**
     * @var ManagerRegistry
     */
    private $doctrineRegistry;

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
     * @param ManagerRegistry        $doctrineRegistry
     */
    public function __construct(
        UserProcessorInterface $userProcessor,
        ManagerRegistry $doctrineRegistry
    )
    {
        $this->userProcessor = $userProcessor;
        $this->doctrineRegistry = $doctrineRegistry;
        $this->metadata['result_code'] = 0;
        $this->metadata['offset'] = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'user_orm';
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
    public function read(array $configuration, LoggerInterface $logger)
    {
        $manager = $this->doctrineRegistry->getManager();
        $repository = $manager->getRepository($configuration['class']);

        if (!$repository instanceof EntityRepository) {
            throw new \InvalidArgumentException(
                'Repository gotten from manager has to be instance of Doctrine\ORM\EntityRepository'
            );
        }

        $read = $this->readFromRepository($configuration, $repository);
        $this->metadata['offset'] += $configuration['batch_size'];

        if (empty($read)) return null;

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
     * @param array            $configuration
     * @param EntityRepository $repository
     *
     * @return array
     */
    private function readFromRepository(array $configuration, $repository)
    {
        $query = $repository->createQueryBuilder('o')
            ->setFirstResult($this->metadata['offset'])
            ->setMaxResults($configuration['batch_size'])
            ->getQuery();

        return $query->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
}
