<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ResourceUpdateHandlerSpec extends ObjectBehavior
{
    function let(ResourceUpdateHandlerInterface $decoratedUpdater, EntityManagerInterface $entityManager): void
    {
        $this->beConstructedWith($decoratedUpdater, $entityManager);
    }

    function it_implements_a_resource_update_handler_interface(): void
    {
        $this->shouldImplement(ResourceUpdateHandlerInterface::class);
    }

    function it_uses_decorated_updater_to_handle_update(
        ResourceUpdateHandlerInterface $decoratedUpdater,
        EntityManagerInterface $entityManager,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager,
    ): void {
        $entityManager->beginTransaction()->shouldBeCalled();
        $decoratedUpdater->handle($resource, $configuration, $manager)->shouldBeCalled();
        $entityManager->commit()->shouldBeCalled();
        $entityManager->rollback()->shouldNotBeCalled();

        $this->handle($resource, $configuration, $manager);
    }

    function it_throws_a_race_condition_exception_if_catch_an_optimistic_lock_exception(
        ResourceUpdateHandlerInterface $decoratedUpdater,
        EntityManagerInterface $entityManager,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager,
    ): void {
        $entityManager->beginTransaction()->shouldBeCalled();
        $decoratedUpdater
            ->handle($resource, $configuration, $manager)
            ->willThrow(OptimisticLockException::class)
        ;
        $entityManager->commit()->shouldNotBeCalled();
        $entityManager->rollback()->shouldBeCalled();

        $this
            ->shouldThrow(RaceConditionException::class)
            ->during('handle', [$resource, $configuration, $manager])
        ;
    }
}
