<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Updater;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdaterInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ResourceUpdater implements ResourceUpdaterInterface
{
    /**
     * @var ResourceUpdaterInterface
     */
    protected $decoratedUpdater;

    /**
     * @param ResourceUpdaterInterface $decoratedUpdater
     */
    public function __construct(ResourceUpdaterInterface $decoratedUpdater)
    {
        $this->decoratedUpdater = $decoratedUpdater;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RaceConditionException
     */
    public function applyTransitionAndFlush(
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        try {
            $this->decoratedUpdater->applyTransitionAndFlush($resource, $configuration, $manager);
        } catch (OptimisticLockException $exception) {
            throw new RaceConditionException();
        }
    }
}
