<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class SequenceProvider implements SequenceProviderInterface
{
    /**
     * @var array
     */
    protected $sequences = array();

    /**
     * @var RepositoryInterface
     */
    protected $sequenceRepository;

    /**
     * @var ObjectManager
     */
    protected $sequenceManager;

    /**
     * @var array
     */
    protected $startIndexes;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $sequenceRepository
     * @param ObjectManager $sequenceManager
     * @param array $startIndexes
     */
    public function __construct(
        RepositoryInterface $sequenceRepository,
        ObjectManager $sequenceManager,
        array $startIndexes = array()
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->manager = $sequenceManager;
        $this->startIndexes = $startIndexes;
    }

    /**
     * {@inheritdoc}
     */
    public function getSequence($type)
    {
        if (isset($this->sequences[$type])) {
            return $this->sequences[$type];
        }

        $sequence = $this->sequenceRepository->findOneBy(array('type' => $type));

        if (null === $sequence) {
            $sequence = $this->sequenceRepository->createNew();
            $sequence->setType($type);

            if (isset($this->startIndexes[$type])) {
                $sequence->setIndex($this->startIndexes[$type]);
            }

            $this->sequenceManager->persist($sequence);
        }

        return $this->sequences[$type] = $sequence;
    }
}
