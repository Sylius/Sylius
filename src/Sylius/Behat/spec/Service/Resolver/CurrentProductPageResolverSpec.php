<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Service\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\Resolver\CurrentProductPageResolver;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @mixin CurrentProductPageResolver
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CurrentProductPageResolverSpec extends ObjectBehavior
{
    function let(CurrentPageResolverInterface $currentPageResolver)
    {
        $this->beConstructedWith($currentPageResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Service\Resolver\CurrentProductPageResolver');
    }

    function it_implements_current_page_resolver_interface()
    {
        $this->shouldImplement(CurrentPageResolverInterface::class);
    }

    function it_delegates_current_page_matching_by_default(
        CurrentPageResolverInterface $currentPageResolver,
        SymfonyPageInterface $createPage,
        SymfonyPageInterface $updatePage
    ) {
        $currentPageResolver->getCurrentPageWithForm([$createPage, $updatePage])->willReturn($createPage);

        $this->getCurrentPageWithForm([$createPage, $updatePage])->shouldReturn($createPage);
    }

    function it_throws_an_exception_if_product_was_not_provided_after_update_page_matched(
        CurrentPageResolverInterface $currentPageResolver,
        UpdateSimpleProductPageInterface $simpleUpdatePage,
        UpdateConfigurableProductPageInterface $configurableUpdatePage
    ) {
        $currentPageResolver->getCurrentPageWithForm([$configurableUpdatePage, $simpleUpdatePage])->willReturn($simpleUpdatePage);

        $this
            ->shouldThrow(new \InvalidArgumentException('It is not possible to determine a product edit page without product provided.'))
            ->during('getCurrentPageWithForm', [[$configurableUpdatePage, $simpleUpdatePage]])
        ;
    }

    function it_matches_proper_update_page_based_on_product_type(
        ProductInterface $product,
        CurrentPageResolverInterface $currentPageResolver,
        UpdateConfigurableProductPageInterface $configurableUpdatePage,
        UpdateSimpleProductPageInterface $simpleUpdatePage
    ) {
        $product->isSimple()->willReturn(false);
        $currentPageResolver->getCurrentPageWithForm([$configurableUpdatePage, $simpleUpdatePage])->willReturn($simpleUpdatePage);

        $this->getCurrentPageWithForm([$configurableUpdatePage, $simpleUpdatePage], $product)->shouldReturn($configurableUpdatePage);
    }

    function it_throws_an_exception_if_product_page_could_not_be_matched(
        ProductInterface $product,
        CurrentPageResolverInterface $currentPageResolver,
        UpdateConfigurableProductPageInterface $configurableUpdatePage
    ) {
        $product->isSimple()->willReturn(true);
        $currentPageResolver->getCurrentPageWithForm([$configurableUpdatePage])->willReturn($configurableUpdatePage);

        $this
            ->shouldThrow(new \InvalidArgumentException('Route name could not be matched to provided pages.'))
            ->during('getCurrentPageWithForm', [[$configurableUpdatePage], $product])
        ;
    }
}
