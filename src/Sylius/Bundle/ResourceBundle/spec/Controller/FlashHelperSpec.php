<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FlashHelperSpec extends ObjectBehavior
{
    function let(Configuration $config, TranslatorInterface $translator, SessionInterface $session)
    {
        $this->beConstructedWith($config, $translator, $session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\FlashHelper');
    }

    function it_has_flash_translated_messages($config, $session, $translator, FlashBag $flashBag)
    {
        $config->getResourceName()->willReturn('product');
        $config->getFlashMessage('create')->willReturn('product.translation.key');
        $translator->trans('product.translation.key', Argument::type('array'), 'flashes')
            ->willReturn('My flashes');

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('success', 'My flashes');

        $this->setFlash('success', 'create')->shouldReturn($this);
    }

    function it_has_flash_messages($config, $session, $translator, FlashBag $flashBag)
    {
        $config->getResourceName()->willReturn('product');
        $config->getFlashMessage('create')->willReturn('product.translation.key');
        $translator->trans('product.translation.key', Argument::type('array'), 'flashes')
            ->willReturn('product.translation.key');

        $translator->trans('sylius.resource.create', Argument::type('array'), 'flashes')
            ->willReturn('My flashes');

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('success', 'My flashes');

        $this->setFlash('success', 'create')->shouldReturn($this);
    }
}
