<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class RequestLocaleProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository)
    {
        $this->beConstructedWith('en_US', $localeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Provider\RequestLocaleProvider');
    }

    function it_subscribes_to_event()
    {
        $this->getSubscribedEvents()->shouldReturn([KernelEvents::REQUEST => [['onKernelRequest', 34]]]);
    }

    function it_returns_fallback_locale()
    {
        $this->getFallbackLocale()->shouldReturn('en_US');
    }

    function it_returns_default_locale()
    {
        $this->getDefaultLocale()->shouldReturn('en_US');
    }
}
