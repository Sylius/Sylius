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

namespace spec\Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\GenerateProductVariantsSubscriber;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Exception\VariantWithNoOptionsValuesException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class GenerateProductVariantsSubscriberSpec extends ObjectBehavior
{
    function let(ProductVariantGeneratorInterface $generator, RequestStack $requestStack): void
    {
        $this->beConstructedWith($generator, $requestStack);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(GenerateProductVariantsSubscriber::class);
    }

    function it_is_a_subscriber(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_events(): void
    {
        $this::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
        ]);
    }

    function it_generates_variants_from_product(
        FormEvent $event,
        ProductInterface $product,
        ProductVariantGeneratorInterface $generator,
    ): void {
        $event->getData()->willReturn($product);

        $generator->generate($product)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_message_to_flash_bag_on_error(
        FormEvent $event,
        ProductInterface $product,
        ProductVariantGeneratorInterface $generator,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $event->getData()->willReturn($product);
        $generator->generate($product)->willThrow(new VariantWithNoOptionsValuesException());
        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);

        $flashBag->add('error', 'sylius.product_variant.cannot_generate_variants')->shouldBeCalled();

        $this->preSetData($event);
    }
}
