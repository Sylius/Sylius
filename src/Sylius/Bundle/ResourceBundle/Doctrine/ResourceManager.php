<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;

/**
 * Doctrine generic resource manager.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceManager implements ResourceManagerInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ResourceInterface $resource)
    {
        $this->objectManager->persist($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource)
    {
        $this->objectManager->remove($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(ResourceInterface $resource)
    {
        $this->objectManager->refresh($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function restore(ResourceInterface $resource)
    {
        if (!$resource instanceof SoftDeletableInterface) {
            throw new \InvalidArgumentException('Resource must implement SoftDeleteableInterface in order to be restored.');
        }

        $resource->setDeletedAt(null);

        $this->objectManager->persist($resource);
    }
}
