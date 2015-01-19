<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomyBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

class TaxonSelectionToCollectionTransformerSpec extends ObjectBehavior
{
    function let(TaxonomyInterface $taxon1, TaxonomyInterface $taxon2)
    {
        $taxon1->getId()->willReturn(1);
        $taxon2->getId()->willReturn(2);

        $this->beConstructedWith(array($taxon1, $taxon2));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Form\DataTransformer\TaxonSelectionToCollectionTransformer');
    }

    function it_does_transform_null_value()
    {
        $this->transform(null)->shouldReturn(array(1 => array(), 2 => array()));
    }

    function it_does_not_transform_string_value()
    {
        $this->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')->duringTransform('');
    }

    function it_does_transform_collection_with_objects_value(
        $taxon1,
        $taxon2,
        TaxonInterface $taxon3,
        TaxonInterface $taxon4,
        Collection $collection
    ) {
        $taxon3->getId()->willReturn(3);
        $taxon4->getId()->willReturn(4);

        $taxon1->getTaxons()->willReturn(array($taxon3));
        $taxon2->getTaxons()->willReturn(array($taxon4));

        $taxon3->getChildren()->willReturn(array());
        $taxon4->getChildren()->willReturn(array());

        $collection->contains($taxon3)->willReturn(true);
        $collection->contains($taxon4)->willReturn(true);

        $this->transform($collection)->shouldReturn(array(1 => array($taxon3), 2 => array($taxon4)));
    }

    function it_does_reverse_transform_empty_value()
    {
        $this->reverseTransform('')->shouldImplement('Doctrine\Common\Collections\Collection');
    }

    function it_does_not_reverse_transform_string_value()
    {
        $this->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringReverseTransform('string');
    }

    function it_does_reverse_transform_array_value(TaxonInterface $taxon)
    {
        $taxon->getId()->willReturn(1);

        $this->reverseTransform(array($taxon))->shouldHaveCount(1);
    }

    function it_does_reverse_transform_array_of_arrays_value(TaxonInterface $taxon3, TaxonInterface $taxon4)
    {
        $taxon3->getId()->willReturn(3);
        $taxon4->getId()->willReturn(4);

        $this->reverseTransform(array(array($taxon3, $taxon4)))->shouldHaveCount(2);
    }
}
