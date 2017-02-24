<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM\Updater;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Updater\ResourceUpdateHandler;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ResourceUpdateHandlerSpec extends ObjectBehavior
{
    function let(ResourceUpdateHandlerInterface $decoratedUpdater)
    {
        $this->beConstructedWith($decoratedUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceUpdateHandler::class);
    }

    function it_implements_a_resource_update_handler_interface()
    {
        $this->shouldImplement(ResourceUpdateHandlerInterface::class);
    }

    function it_uses_decorated_updater_to_handle_update(
        ResourceUpdateHandlerInterface $decoratedUpdater,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        $decoratedUpdater->handle($resource, $configuration, $manager);

        $this->handle($resource, $configuration, $manager);
    }

    function it_throws_a_race_condition_exception_if_catch_an_optimistic_lock_exception(
        ResourceUpdateHandlerInterface $decoratedUpdater,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
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
