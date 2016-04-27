<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\EventListener\ThemeSynchronizationListener;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin ThemeSynchronizationListener
 *
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class ThemeSynchronizationListenerSpec extends ObjectBehavior
{
    function let(ThemeSynchronizerInterface $themeSynchronizer) {
        $this->beConstructedWith($themeSynchronizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ThemeSynchronizationListener::class);
    }

    function it_throws_exception_if_subject_is_not_theme(GenericEvent $event, \stdClass $nonTheme)
    {
        $event->getSubject()->willReturn($nonTheme);

        $this->shouldThrow(UnexpectedTypeException::class)
            ->duringSynchronizeTheme($event);
    }

    function it_synchronizes_theme(GenericEvent $event, ThemeInterface $theme, $themeSynchronizer)
    {
        $event->getSubject()->willReturn($theme);
        $themeSynchronizer->synchronize($theme)->shouldBeCalled();

        $this->synchronizeTheme($event);
    }

    function it_synchronizes_themes(GenericEvent $event, $themeSynchronizer)
    {
        $themeSynchronizer->synchronize()->shouldBeCalled();

        $this->synchronize($event);
    }
}
