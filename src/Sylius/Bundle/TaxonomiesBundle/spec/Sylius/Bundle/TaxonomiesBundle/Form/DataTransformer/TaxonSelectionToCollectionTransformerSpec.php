<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomiesBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

// Since the root namespace "spec" is not in our autoload
require_once __DIR__.DIRECTORY_SEPARATOR.'FakeEntity.php';

class TaxonSelectionToCollectionTransformerSpec extends ObjectBehavior
{
    function let(FakeEntity $entityOne, FakeEntity $entityTwo)
    {
        $entityOne->getId()->willReturn(1);
        $entityTwo->getId()->willReturn(2);

        $this->beConstructedWith(array($entityOne, $entityTwo));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Form\DataTransformer\TaxonSelectionToCollectionTransformer');
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
        FakeEntity $entityOne, FakeEntity $entityTwo, FakeEntity $entityThree, FakeEntity $entityFour, Collection $collection
    )
    {
        $entityThree->getId()->willReturn(3);
        $entityFour->getId()->willReturn(4);

        $entityOne->getTaxons()->willReturn(array($entityThree));
        $entityTwo->getTaxons()->willReturn(array($entityFour));

        $collection->contains($entityThree)->willReturn(true);
        $collection->contains($entityFour)->willReturn(true);

        $this->transform($collection)->shouldReturn(array(1 => array($entityThree), 2 => array($entityFour)));
    }

    function it_does_reverse_transform_empty_value()
    {
        $this->reverseTransform('')->shouldImplement('Doctrine\Common\Collections\Collection');
    }

    function it_does_not_reverse_transform_string_value()
    {
        $this->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')->duringReverseTransform('string');
    }

    function it_does_reverse_transform_array_value(FakeEntity $entity)
    {
        $entity->getId()->willReturn(1);

        $this->reverseTransform(array($entity))->shouldHaveCount(1);
    }

    function it_does_reverse_transform_array_of_arrays_value(FakeEntity $entityThree, FakeEntity $entityFour)
    {
        $entityThree->getId()->willReturn(3);
        $entityFour->getId()->willReturn(4);

        $this->reverseTransform(array(array($entityThree, $entityFour)))->shouldHaveCount(2);
    }
}
