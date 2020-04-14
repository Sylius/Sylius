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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class ProductSlugEventSubscriberSpec extends ObjectBehavior
{
    function let(SlugGeneratorInterface $slugGenerator): void
    {
        $this->beConstructedWith($slugGenerator);
    }

    function it_generates_slug_for_product_with_name_and_empty_slug(
        SlugGeneratorInterface $slugGenerator,
        ProductInterface $product,
        ProductTranslationInterface $productTranslation,
        Request $request,
        ViewEvent $event
    ): void {
        $event->getControllerResult()->willReturn($product);
        $event->getRequest()->willReturn($request);
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $product->getTranslations()->willReturn(new ArrayCollection([$productTranslation->getWrappedObject()]));
        $productTranslation->getSlug()->willReturn(null);
        $productTranslation->getName()->willReturn('Audi RS7');

        $slugGenerator->generate('Audi RS7')->willReturn('audi-rs7');

        $productTranslation->setSlug('audi-rs7')->shouldBeCalled();
        $event->setControllerResult($product)->shouldBeCalled();

        $this->generateSlug($event);
    }

    function it_does_nothing_if_the_product_has_slug(
        SlugGeneratorInterface $slugGenerator,
        ProductInterface $product,
        ProductTranslationInterface $productTranslation,
        Request $request,
        ViewEvent $event
    ): void {
        $event->getControllerResult()->willReturn($product);
        $event->getRequest()->willReturn($request);
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $product->getTranslations()->willReturn(new ArrayCollection([$productTranslation->getWrappedObject()]));
        $productTranslation->getSlug()->willReturn('audi-rs7');

        $productTranslation->getName()->shouldNotBeCalled();
        $slugGenerator->generate(Argument::any())->shouldNotBeCalled();
        $productTranslation->setSlug(Argument::any())->shouldNotBeCalled();

        $event->setControllerResult($product)->shouldBeCalled();

        $this->generateSlug($event);
    }

    function it_does_nothing_if_the_product_has_no_name(
        SlugGeneratorInterface $slugGenerator,
        ProductInterface $product,
        ProductTranslationInterface $productTranslation,
        Request $request,
        ViewEvent $event
    ): void {
        $event->getControllerResult()->willReturn($product);
        $event->getRequest()->willReturn($request);
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $product->getTranslations()->willReturn(new ArrayCollection([$productTranslation->getWrappedObject()]));
        $productTranslation->getSlug()->willReturn(null);
        $productTranslation->getName()->willReturn(null);

        $slugGenerator->generate(Argument::any())->shouldNotBeCalled();
        $productTranslation->setSlug(Argument::any())->shouldNotBeCalled();

        $event->setControllerResult($product)->shouldBeCalled();

        $this->generateSlug($event);
    }
}
