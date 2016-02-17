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
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class TaxonSelectionToCollectionTransformerSpec extends ObjectBehavior
{
    function let(TaxonomyInterface $entityOne, TaxonomyInterface $entityTwo)
    {
        $entityOne->getId()->willReturn(1);
        $entityTwo->getId()->willReturn(2);

        $this->beConstructedWith([$entityOne, $entityTwo]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Form\DataTransformer\TaxonSelectionToCollectionTransformer');
    }

    function it_does_transform_null_value()
    {
        $this->transform(null)->shouldReturn([1 => [], 2 => []]);
    }

    function it_does_not_transform_string_value()
    {
        $this->shouldThrow(UnexpectedTypeException::class)->duringTransform('');
    }

    function it_does_transform_collection_with_objects_value(
        $entityOne,
        $entityTwo,
        TaxonInterface $entityThree,
        TaxonInterface $entityFour,
        Collection $collection
    ) {
        $entityThree->getId()->willReturn(3);
        $entityFour->getId()->willReturn(4);

        $entityOne->getTaxons()->willReturn([$entityThree]);
        $entityTwo->getTaxons()->willReturn([$entityFour]);

        $entityThree->getChildren()->willReturn([]);
        $entityFour->getChildren()->willReturn([]);

        $collection->contains($entityThree)->willReturn(true);
        $collection->contains($entityFour)->willReturn(true);

        $this->transform($collection)->shouldReturn([1 => [$entityThree], 2 => [$entityFour]]);
    }

    function it_does_reverse_transform_empty_value()
    {
        $this->reverseTransform('')->shouldImplement(Collection::class);
    }

    function it_does_not_reverse_transform_string_value()
    {
        $this->shouldThrow(UnexpectedTypeException::class)
            ->duringReverseTransform('string');
    }

    function it_does_reverse_transform_array_value(TaxonInterface $entity)
    {
        $entity->getId()->willReturn(1);

        $this->reverseTransform([$entity])->shouldHaveCount(1);
    }

    function it_does_reverse_transform_array_of_arrays_value(TaxonInterface $entityThree, TaxonInterface $entityFour)
    {
        $entityThree->getId()->willReturn(3);
        $entityFour->getId()->willReturn(4);

        $this->reverseTransform([[$entityThree, $entityFour]])->shouldHaveCount(2);
    }
}
