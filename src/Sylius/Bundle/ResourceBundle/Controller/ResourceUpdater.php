<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ResourceUpdater implements ResourceUpdaterInterface
{
    /**
     * @var StateMachineInterface
     */
    private $stateMachine;

    /**
     * @param StateMachineInterface $stateMachine
     */
    public function __construct(StateMachineInterface $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTransitionAndFlush(
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        if ($configuration->hasStateMachine()) {
            $this->stateMachine->apply($configuration, $resource);
        }

        $manager->flush();
    }
}
