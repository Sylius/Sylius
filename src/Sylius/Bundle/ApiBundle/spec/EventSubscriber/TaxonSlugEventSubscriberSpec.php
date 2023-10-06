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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class TaxonSlugEventSubscriberSpec extends ObjectBehavior
{
    function let(TaxonSlugGeneratorInterface $taxonSlugGenerator): void
    {
        $this->beConstructedWith($taxonSlugGenerator);
    }

    function it_generates_slug_for_taxon_with_name_and_empty_slug(
        TaxonSlugGeneratorInterface $taxonSlugGenerator,
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $taxon->getTranslations()->willReturn(new ArrayCollection([$taxonTranslation->getWrappedObject()]));
        $taxonTranslation->getSlug()->willReturn(null);
        $taxonTranslation->getName()->willReturn('PHP Mug');
        $taxonTranslation->getLocale()->willReturn('en_US');

        $taxonSlugGenerator->generate($taxon, 'en_US')->willReturn('php-mug');

        $taxonTranslation->setSlug('php-mug')->shouldBeCalled();

        $this->generateSlug(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $taxon->getWrappedObject(),
        ));
    }

    function it_does_nothing_if_the_taxon_has_slug(
        TaxonSlugGeneratorInterface $taxonSlugGenerator,
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $taxon->getTranslations()->willReturn(new ArrayCollection([$taxonTranslation->getWrappedObject()]));
        $taxonTranslation->getSlug()->willReturn('php-mug');

        $taxonTranslation->getName()->shouldNotBeCalled();
        $taxonSlugGenerator->generate(Argument::any())->shouldNotBeCalled();
        $taxonTranslation->setSlug(Argument::any())->shouldNotBeCalled();

        $this->generateSlug(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $taxon->getWrappedObject(),
        ));
    }

    function it_does_nothing_if_the_taxon_has_no_name(
        TaxonSlugGeneratorInterface $taxonSlugGenerator,
        TaxonInterface $taxon,
        TaxonTranslationInterface $taxonTranslation,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $taxon->getTranslations()->willReturn(new ArrayCollection([$taxonTranslation->getWrappedObject()]));
        $taxonTranslation->getSlug()->willReturn(null);
        $taxonTranslation->getName()->willReturn(null);

        $taxonSlugGenerator->generate(Argument::any())->shouldNotBeCalled();
        $taxonTranslation->setSlug(Argument::any())->shouldNotBeCalled();

        $this->generateSlug(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $taxon->getWrappedObject(),
        ));
    }
}
