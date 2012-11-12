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
use Sylius\Bundle\ResourceBundle\Manager\ResourceManager as BaseResourceManager;
use Sylius\Bundle\ResourceBundle\Model\ResourceInterface;

/**
 * Doctrine resource manager class.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceManager extends BaseResourceManager
{
    protected $objectManager;

    public function __construct(ObjectManager $objectManager, $className)
    {
        $this->objectManager = $objectManager;

        parent::__construct($className);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ResourceInterface $resource, $flush = true)
    {
        $this->objectManager->persist($resource);

        if ($flush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource, $flush = true)
    {
        $this->objectManager->remove($resource);

        if ($flush) {
            $this->objectManager->flush();
        }
    }
}
