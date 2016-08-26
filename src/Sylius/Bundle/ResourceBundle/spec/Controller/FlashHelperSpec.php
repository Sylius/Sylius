<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelper;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @mixin FlashHelper
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class FlashHelperSpec extends ObjectBehavior
{
    function let(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->beConstructedWith($session, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\FlashHelper');
    }

    function it_implements_flash_helper_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Controller\FlashHelperInterface');
    }

    function it_adds_appropriate_flash_for_html_request(
        SessionInterface $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ) {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getHumanizedName()->willReturn('product');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->isHtmlRequest()->willReturn(true);

        $session->getBag('flashes')->willReturn($flashBag);
        $requestConfiguration->getFlashMessage(ResourceActions::CREATE)->willReturn('sylius.product.create');
        $translator->trans('sylius.product.create', ['%resource%' => 'Product'], 'flashes')->willReturn('Product has been created successfully, bueno!');

        $flashBag->add('success', 'Product has been created successfully, bueno!')->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, ResourceActions::CREATE, $resource);
    }

    function it_uses_default_translation_if_message_is_not_translated(
        SessionInterface $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ) {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getHumanizedName()->willReturn('product');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->isHtmlRequest()->willReturn(true);

        $session->getBag('flashes')->willReturn($flashBag);
        $requestConfiguration->getFlashMessage(ResourceActions::CREATE)->willReturn('sylius.product.create');
        $translator->trans('sylius.product.create', ['%resource%' => 'Product'], 'flashes')->willReturn('sylius.product.create');
        $translator->trans('sylius.resource.create', ['%resource%' => 'Product'], 'flashes')->willReturn('Product has been successfully created.');

        $flashBag->add('success', 'Product has been successfully created.')->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, ResourceActions::CREATE, $resource);
    }

    function it_adds_flash_from_event(
        SessionInterface $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        RequestConfiguration $requestConfiguration,
        ResourceControllerEvent $event
    ) {
        $event->getMessage()->willReturn('sylius.channel.cannot_be_deleted');
        $event->getMessageType()->willReturn(ResourceControllerEvent::TYPE_WARNING);
        $event->getMessageParameters()->willReturn(['%name%' => 'Germany Sylius Webshop']);

        $session->getBag('flashes')->willReturn($flashBag);
        $translator->trans('sylius.channel.cannot_be_deleted', ['%name%' => 'Germany Sylius Webshop'], 'flashes')->willReturn('Channel "Germany Sylius Webshop" cannot be deleted.');

        $flashBag->add(ResourceControllerEvent::TYPE_WARNING, 'Channel "Germany Sylius Webshop" cannot be deleted.')->shouldBeCalled();

        $this->addFlashFromEvent($requestConfiguration, $event);
    }
}
