<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxonomy\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\Taxon;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyTranslation;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonomySpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxonomy\Model\Taxonomy');
    }

    public function it_implements_Sylius_taxonomy_interface()
    {
        $this->shouldImplement('Sylius\Component\Taxonomy\Model\TaxonomyInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_calls_translation_to_string(TaxonomyTranslation $translation)
    {
        $translation->getLocale()->willReturn('en_US');
        $translation->setTranslatable($this)->shouldBeCalled();

        $this->addTranslation($translation);
        $translation->__toString()->shouldBeCalled();

        $this->__toString();
    }

    public function it_has_no_root_by_default()
    {
        $this->getRoot()->shouldReturn(null);
    }

    public function it_allows_setting_the_root_taxon(TaxonInterface $taxon)
    {
        $this->setRoot($taxon);
        $this->getRoot()->shouldReturn($taxon);
    }

    public function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable(Taxon $taxon)
    {
        $taxon->setName('Brand')->shouldBeCalled();
        $taxon->setTaxonomy($this)->shouldBeCalled();

        $taxon->setCurrentLocale('en_US')->shouldBeCalled();
        $taxon->setFallbackLocale('en_US')->shouldBeCalled();

        $this->setRoot($taxon);

        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    public function it_also_sets_name_on_the_root_taxon(Taxon $taxon)
    {
        $taxon->setName('Category')->shouldBeCalled();
        $taxon->setTaxonomy($this)->shouldBeCalled();

        $taxon->setCurrentLocale('en_US')->shouldBeCalled();
        $taxon->setFallbackLocale('en_US')->shouldBeCalled();

        $this->setRoot($taxon);

        $this->setName('Category');
    }

    public function it_delegates_the_hasTaxon_method_to_root_taxon(TaxonInterface $root, TaxonInterface $taxon)
    {
        $this->setRoot($root);

        $root->hasChild($taxon)->willReturn(true);
        $this->hasTaxon($taxon)->shouldReturn(true);

        $root->hasChild($taxon)->willReturn(false);
        $this->hasTaxon($taxon)->shouldReturn(false);
    }

    public function it_delegates_addTaxon_method_to_root_taxon(TaxonInterface $root, TaxonInterface $taxon)
    {
        $this->setRoot($root);

        $root->addChild($taxon)->shouldBeCalled();
        $this->addTaxon($taxon);
    }

    public function it_delegates_removeTaxon_method_to_root_taxon(TaxonInterface $root, TaxonInterface $taxon)
    {
        $this->setRoot($root);

        $root->removeChild($taxon)->shouldBeCalled();
        $this->removeTaxon($taxon);
    }
}
