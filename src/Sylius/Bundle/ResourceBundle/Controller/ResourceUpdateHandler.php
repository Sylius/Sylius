<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ResourceUpdateHandler implements ResourceUpdateHandlerInterface
{
    /** @var StateMachineInterface */
    private $stateMachine;

    public function __construct(StateMachineInterface $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ): void {
        if ($configuration->hasStateMachine()) {
            $this->stateMachine->apply($configuration, $resource);
        }

        $manager->flush();
    }
}
