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

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelper;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelperInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class FlashHelperSpec extends ObjectBehavior
{
    function let(SessionInterface $session, TranslatorInterface $translator): void
    {
        $this->beConstructedWith($session, $translator, 'en');
    }

    function it_implements_flash_helper_interface(): void
    {
        $this->shouldImplement(FlashHelperInterface::class);
    }

    function it_adds_resource_message_by_default(
        SessionInterface $session,
        TranslatorBagInterface $translator,
        MessageCatalogueInterface $messageCatalogue,
        FlashBagInterface $flashBag,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getHumanizedName()->willReturn('product');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->getFlashMessage(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $translator->getCatalogue('en')->willReturn($messageCatalogue);
        $messageCatalogue->has('sylius.product.create', 'flashes')->willReturn(false);

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add(
            'success',
            [
                'message' => 'sylius.resource.create',
                'parameters' => ['%resource%' => 'Product'],
            ]
        )->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, ResourceActions::CREATE, $resource);
    }

    function it_adds_resource_message_when_catalogue_is_unavailable_and_given_message_cannot_be_translated(
        SessionInterface $session,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): void {
        $parameters = ['%resource%' => 'Product'];

        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getHumanizedName()->willReturn('product');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->getFlashMessage(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $translator->trans('sylius.product.create', $parameters, 'flashes')->willReturn('sylius.product.create');

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add(
            'success',
            [
                'message' => 'sylius.resource.create',
                'parameters' => $parameters,
            ]
        )->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, ResourceActions::CREATE, $resource);
    }

    function it_adds_resource_message_when_catalogue_is_unavailable_and_given_message_can_be_translated(
        SessionInterface $session,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): void {
        $parameters = ['%resource%' => 'Spoon'];

        $metadata->getApplicationName()->willReturn('app');
        $metadata->getHumanizedName()->willReturn('spoon');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->getFlashMessage(ResourceActions::CREATE)
            ->willReturn('%resource% is the best cutlery of them all!')
        ;

        $translator->trans('%resource% is the best cutlery of them all!', $parameters, 'flashes')
            ->willReturn('Spoon is the best cutlery of them all!')
        ;

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add(
            'success',
            [
                'message' => '%resource% is the best cutlery of them all!',
                'parameters' => $parameters,
            ]
        )->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, ResourceActions::CREATE, $resource);
    }

    function it_adds_resource_message_if_message_was_not_found_in_the_catalogue(
        SessionInterface $session,
        TranslatorBagInterface $translator,
        MessageCatalogueInterface $messageCatalogue,
        FlashBagInterface $flashBag,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getHumanizedName()->willReturn('product');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->getFlashMessage(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $translator->getCatalogue('en')->willReturn($messageCatalogue);

        $messageCatalogue->has('sylius.product.create', 'flashes')->willReturn(false);

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add(
            'success',
            [
                'message' => 'sylius.resource.create',
                'parameters' => ['%resource%' => 'Product'],
            ]
        )->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, ResourceActions::CREATE, $resource);
    }

    function it_adds_overwritten_message(
        SessionInterface $session,
        TranslatorBagInterface $translator,
        MessageCatalogueInterface $messageCatalogue,
        FlashBagInterface $flashBag,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getHumanizedName()->willReturn('product');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->getFlashMessage(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $translator->getCatalogue('en')->willReturn($messageCatalogue);

        $messageCatalogue->has('sylius.product.create', 'flashes')->willReturn(true);

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('success', 'sylius.product.create')->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, ResourceActions::CREATE, $resource);
    }

    function it_adds_custom_message(
        SessionInterface $session,
        TranslatorBagInterface $translator,
        MessageCatalogueInterface $messageCatalogue,
        FlashBagInterface $flashBag,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): void {
        $metadata->getApplicationName()->willReturn('app');
        $metadata->getHumanizedName()->willReturn('book');

        $requestConfiguration->getMetadata()->willReturn($metadata);
        $requestConfiguration->getFlashMessage('send')->willReturn('app.book.send');

        $translator->getCatalogue('en')->willReturn($messageCatalogue);

        $messageCatalogue->has('app.book.send', 'flashes')->willReturn(true);

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('success', 'app.book.send')->shouldBeCalled();

        $this->addSuccessFlash($requestConfiguration, 'send', $resource);
    }

    function it_adds_message_from_event(
        SessionInterface $session,
        FlashBagInterface $flashBag,
        RequestConfiguration $requestConfiguration,
        ResourceControllerEvent $event
    ): void {
        $event->getMessage()->willReturn('sylius.channel.cannot_be_deleted');
        $event->getMessageType()->willReturn(ResourceControllerEvent::TYPE_WARNING);
        $event->getMessageParameters()->willReturn(['%name%' => 'Germany Sylius Webshop']);

        $session->getBag('flashes')->willReturn($flashBag);

        $flashBag->add(ResourceControllerEvent::TYPE_WARNING,
            [
                'message' => 'sylius.channel.cannot_be_deleted',
                'parameters' => ['%name%' => 'Germany Sylius Webshop']
            ]
        )->shouldBeCalled();

        $this->addFlashFromEvent($requestConfiguration, $event);
    }
}
