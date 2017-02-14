<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Updater;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\OptimisticLockException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Updater\ResourceUpdater;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdaterInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ResourceUpdaterSpec extends ObjectBehavior
{
    function let(ResourceUpdaterInterface $decoratedUpdater)
    {
        $this->beConstructedWith($decoratedUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceUpdater::class);
    }

    function it_implements_a_resource_updater_interface()
    {
        $this->shouldImplement(ResourceUpdaterInterface::class);
    }

    function it_uses_decorated_updater_to_apply_transition_and_flush(
        ResourceUpdaterInterface $decoratedUpdater,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        $decoratedUpdater->applyTransitionAndFlush($resource, $configuration, $manager);

        $this->applyTransitionAndFlush($resource, $configuration, $manager);
    }

    function it_throws_a_race_condition_exception_if_catch_optimistic_lock_exception(
        ResourceUpdaterInterface $decoratedUpdater,
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        $decoratedUpdater
            ->applyTransitionAndFlush($resource, $configuration, $manager)
            ->willThrow(OptimisticLockException::class)
        ;

        $this
            ->shouldThrow(RaceConditionException::class)
            ->during('applyTransitionAndFlush', [$resource, $configuration, $manager])
        ;
    }
}
