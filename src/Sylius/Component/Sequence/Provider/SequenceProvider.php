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

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
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
     * Constructor.
     *
     * @param RepositoryInterface $sequenceRepository
     * @param ObjectManager $sequenceManager
     */
    public function __construct(
        RepositoryInterface $sequenceRepository,
        ObjectManager $sequenceManager
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceManager = $sequenceManager;
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
            $sequenceClass = $this->sequenceRepository->getClassName();
            $sequence = new $sequenceClass($type);

            $this->sequenceManager->persist($sequence);
        }

        return $this->sequences[$type] = $sequence;
    }
}
