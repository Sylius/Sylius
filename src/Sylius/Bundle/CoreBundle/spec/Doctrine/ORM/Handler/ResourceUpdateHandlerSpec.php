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

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ResourceUpdateHandlerSpec extends ObjectBehavior
{
    function let(ResourceUpdateHandlerInterface $decoratedUpdater): void
    {
        $this->beConstructedWith($decoratedUpdater);
    }

    function it_implements_a_resource_update_handler_interface(): void
    {
        $this->shouldImplement(ResourceUpdateHandlerInterface::class);
    }

    function it_uses_decorated_updater_to_handle_update(
        ResourceUpdateHandlerInterface $decoratedUpdater,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ): void {
        $decoratedUpdater->handle($resource, $configuration, $manager);

        $this->handle($resource, $configuration, $manager);
    }

    function it_throws_a_race_condition_exception_if_catch_an_optimistic_lock_exception(
        ResourceUpdateHandlerInterface $decoratedUpdater,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ): void {
        $decoratedUpdater
            ->handle($resource, $configuration, $manager)
            ->willThrow(OptimisticLockException::class)
        ;

        $this
            ->shouldThrow(RaceConditionException::class)
            ->during('handle', [$resource, $configuration, $manager])
        ;
    }
}
