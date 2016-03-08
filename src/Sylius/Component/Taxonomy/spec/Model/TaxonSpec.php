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
use Sylius\Component\Taxonomy\Model\TaxonInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxonomy\Model\Taxon');
    }

    function it_implements_Sylius_taxon_interface()
    {
        $this->shouldImplement(TaxonInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_mutable_code()
    {
        $this->setCode('TX2');
        $this->getCode()->shouldReturn('TX2');
    }

    function it_has_no_parent_by_default()
    {
        $this->getParent()->shouldReturn(null);
    }

    function its_parent_is_mutable(TaxonInterface $taxon)
    {
        $this->setParent($taxon);
        $this->getParent()->shouldReturn($taxon);
    }

    function it_is_root_by_default()
    {
        $this->shouldBeRoot();
    }

    function it_is_not_root_when_has_parent(TaxonInterface $taxon)
    {
        $this->setParent($taxon);
        $this->shouldNotBeRoot();
    }

    function it_is_root_when_has_no_parent(TaxonInterface $taxon)
    {
        $this->shouldBeRoot();

        $this->setParent($taxon);
        $this->shouldNotBeRoot();
        $this->setParent(null);

        $this->shouldBeRoot();
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Brand');
        $this->getName()->shouldReturn('Brand');
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('T-Shirt material');
        $this->__toString()->shouldReturn('T-Shirt material');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('This is a list of brands.');
        $this->getDescription()->shouldReturn('This is a list of brands.');
    }

    function it_has_no_slug_by_default()
    {
        $this->getSlug()->shouldReturn(null);
    }

    function its_slug_is_mutable()
    {
        $this->setSlug('t-shirts');
        $this->getSlug()->shouldReturn('t-shirts');
    }

    function it_has_no_permalink_by_default()
    {
        $this->getPermalink()->shouldReturn(null);
    }

    function its_permalink_is_mutable()
    {
        $this->setPermalink('woman-clothing');
        $this->getPermalink()->shouldReturn('woman-clothing');
    }

    function it_initializes_child_taxon_collection_by_default()
    {
        $this->getChildren()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_allows_to_check_if_given_taxon_is_its_child(TaxonInterface $taxon)
    {
        $this->hasChild($taxon)->shouldReturn(false);
    }

    function it_allows_to_add_child_taxons(TaxonInterface $taxon)
    {
        $taxon->setParent($this)->shouldBeCalled();

        $this->addChild($taxon);
    }

    function it_allows_to_remove_child_taxons(TaxonInterface $taxon)
    {
        $taxon->setParent($this)->shouldBeCalled();

        $this->addChild($taxon);

        $taxon->setParent(null)->shouldBeCalled();

        $this->removeChild($taxon);
    }
}
