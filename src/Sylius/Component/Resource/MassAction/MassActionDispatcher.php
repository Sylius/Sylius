<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\MassAction\MassActionDispatcherInterface;

/**
 * Mass action dispatcher.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MassActionDispatcher implements MassActionDispatcherInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $actionsRegistry;

    /**
     * @param ServiceRegistryInterface $actionsRegistry
     */
    public function __construct(ServiceRegistryInterface $actionsRegistry)
    {
        $this->actionTypes = $actionsRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($type, array $resources)
    {
        $action = $this->actionsRegistry->get($type);

        return $action->execute($resources);
    }
}
