<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductTranslation;
use Sylius\Component\Product\Model\ProductTranslationInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class ProductTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductTranslation::class);
    }

    function it_implements_Sylius_product_translation_interface()
    {
        $this->shouldImplement(ProductTranslationInterface::class);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Super product');
        $this->getName()->shouldReturn('Super product');
    }

    function it_has_no_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    function its_slug_is_mutable()
    {
        $this->setSlug('super-product');
        $this->getSlug()->shouldReturn('super-product');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('This product is super cool because...');
        $this->getDescription()->shouldReturn('This product is super cool because...');
    }

    function it_has_no_meta_keywords_by_default()
    {
        $this->getMetaKeywords()->shouldReturn(null);
    }

    function its_meta_keywords_is_mutable()
    {
        $this->setMetaKeywords('foo, bar, baz');
        $this->getMetaKeywords()->shouldReturn('foo, bar, baz');
    }

    function it_has_no_meta_description_by_default()
    {
        $this->getMetaDescription()->shouldReturn(null);
    }

    function its_meta_description_is_mutable()
    {
        $this->setMetaDescription('Super product');
        $this->getMetaDescription()->shouldReturn('Super product');
    }
}
