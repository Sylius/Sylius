<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductTaxon::class);
    }

    function it_implements_product_taxon_interface()
    {
        $this->shouldImplement(ProductTaxonInterface::class);
    }

    function it_implements_taxon_interface()
    {
        $this->shouldImplement(TaxonInterface::class);
    }

    function it_has_mutable_product_field(ProductInterface $product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);
    }

    function it_has_mutable_taxon_field(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $this->getTaxon()->shouldReturn($taxon);
    }

    function it_has_mutable_position_field()
    {
        $this->setPosition(1);
        $this->getPosition()->shouldReturn(1);
    }

    function it_proxies_taxon_get_code(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getCode()->shouldBeCalled()->willReturn('TX1');
        $this->getCode()->shouldReturn('TX1');
    }

    function it_proxies_taxon_set_code(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setCode('TX2')->shouldBeCalled();
        $this->setCode('TX2');
    }

    function it_proxies_taxon_get_images(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getImages()->shouldBeCalled()->willReturn([]);
        $this->getImages()->shouldReturn([]);
    }

    function it_proxies_taxon_get_images_by_type(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getImagesByType('main')->shouldBeCalled()->willReturn([]);
        $this->getImagesByType('main')->shouldReturn([]);
    }

    function it_proxies_taxon_has_images(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->hasImages()->shouldBeCalled()->willReturn(true);
        $this->hasImages()->shouldReturn(true);
    }

    function it_proxies_taxon_has_image(TaxonInterface $taxon, ImageInterface $image)
    {
        $this->setTaxon($taxon);
        $taxon->hasImage($image)->shouldBeCalled()->willReturn(true);
        $this->hasImage($image)->shouldReturn(true);
    }

    function it_proxies_taxon_remove_image(TaxonInterface $taxon, ImageInterface $image)
    {
        $this->setTaxon($taxon);
        $taxon->removeImage($image)->shouldBeCalled();
        $this->removeImage($image);
    }

    function it_proxies_taxon_get_slug(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getSlug()->shouldBeCalled()->willReturn('t-shirts');
        $this->getSlug()->shouldReturn('t-shirts');
    }

    function it_proxies_taxon_set_slug(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setSlug('books')->shouldBeCalled();
        $this->setSlug('books');
    }

    function it_proxies_taxon_is_root(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->isRoot()->shouldBeCalled()->willReturn(false);
        $this->isRoot()->shouldReturn(false);
    }

    function it_proxies_taxon_get_root(TaxonInterface $taxon, TaxonInterface $root)
    {
        $this->setTaxon($taxon);
        $taxon->getRoot()->shouldBeCalled()->willReturn($root);
        $this->getRoot()->shouldReturn($root);
    }

    function it_proxies_taxon_get_parent(TaxonInterface $taxon, TaxonInterface $parent)
    {
        $this->setTaxon($taxon);
        $taxon->getParent()->shouldBeCalled()->willReturn($parent);
        $this->getParent()->shouldReturn($parent);
    }

    function it_proxies_taxon_set_parent(TaxonInterface $taxon, TaxonInterface $parent)
    {
        $this->setTaxon($taxon);
        $taxon->setParent($parent)->shouldBeCalled();
        $this->setParent($parent);
    }

    function it_proxies_taxon_get_parents(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getParents()->shouldBeCalled()->willReturn([]);
        $this->getParents()->shouldReturn([]);
    }

    function it_proxies_taxon_get_children(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getChildren()->shouldBeCalled()->willReturn([]);
        $this->getChildren()->shouldReturn([]);
    }

    function it_proxies_taxon_has_child(TaxonInterface $taxon, TaxonInterface $child)
    {
        $this->setTaxon($taxon);
        $taxon->hasChild($child)->shouldBeCalled()->willReturn(true);
        $this->hasChild($child)->shouldReturn(true);
    }

    function it_proxies_taxon_add_child(TaxonInterface $taxon, TaxonInterface $child)
    {
        $this->setTaxon($taxon);
        $taxon->addChild($child)->shouldBeCalled();
        $this->addChild($child);
    }

    function it_proxies_taxon_remove_child(TaxonInterface $taxon, TaxonInterface $child)
    {
        $this->setTaxon($taxon);
        $taxon->removeChild($child)->shouldBeCalled();
        $this->removeChild($child);
    }

    function it_proxies_taxon_get_left(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getLeft()->shouldBeCalled()->willReturn(2);
        $this->getLeft()->shouldReturn(2);
    }

    function it_proxies_taxon_set_left(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setLeft(3)->shouldBeCalled();
        $this->setLeft(3);
    }

    function it_proxies_taxon_get_right(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getRight()->shouldBeCalled()->willReturn(1);
        $this->getRight()->shouldReturn(1);
    }

    function it_proxies_taxon_set_right(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setRight(3)->shouldBeCalled();
        $this->setRight(3);
    }

    function it_proxies_taxon_get_level(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getLevel()->shouldBeCalled()->willReturn(1);
        $this->getLevel()->shouldReturn(1);
    }

    function it_proxies_taxon_set_level(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setLevel(3)->shouldBeCalled();
        $this->setLevel(3);
    }

    function it_proxies_taxon_get_name(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getName()->shouldBeCalled()->willReturn('Books');
        $this->getName()->shouldReturn('Books');
    }

    function it_proxies_taxon_set_name(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setName('T-Shirts')->shouldBeCalled();
        $this->setName('T-Shirts');
    }

    function it_proxies_taxon_get_description(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getDescription()->shouldBeCalled()->willReturn('Tan?');
        $this->getDescription()->shouldReturn('Tan?');
    }

    function it_proxies_taxon_set_description(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setDescription('Chosseline')->shouldBeCalled();
        $this->setDescription('Chosseline');
    }

    function it_proxies_taxon_get_translations(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->getTranslations()->shouldBeCalled()->willReturn([]);
        $this->getTranslations()->shouldReturn([]);
    }

    function it_proxies_taxon_get_translation(TaxonInterface $taxon, TranslationInterface $translation)
    {
        $this->setTaxon($taxon);
        $taxon->getTranslation('en')->shouldBeCalled()->willReturn($translation);
        $this->getTranslation('en')->shouldReturn($translation);
    }

    function it_proxies_taxon_has_translation(TaxonInterface $taxon, TranslationInterface $translation)
    {
        $this->setTaxon($taxon);
        $taxon->hasTranslation($translation)->shouldBeCalled()->willReturn(true);
        $this->hasTranslation($translation)->shouldReturn(true);
    }

    function it_proxies_taxon_add_translation(TaxonInterface $taxon, TranslationInterface $translation)
    {
        $this->setTaxon($taxon);
        $taxon->addTranslation($translation)->shouldBeCalled();
        $this->addTranslation($translation);
    }

    function it_proxies_taxon_remove_translation(TaxonInterface $taxon, TranslationInterface $translation)
    {
        $this->setTaxon($taxon);
        $taxon->removeTranslation($translation)->shouldBeCalled();
        $this->removeTranslation($translation);
    }

    function it_proxies_taxon_set_current_locale(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setCurrentLocale('nl')->shouldBeCalled();
        $this->setCurrentLocale('nl');
    }

    function it_proxies_taxon_set_fallback_locale(TaxonInterface $taxon)
    {
        $this->setTaxon($taxon);
        $taxon->setFallbackLocale('de')->shouldBeCalled();
        $this->setFallbackLocale('de');
    }
}
