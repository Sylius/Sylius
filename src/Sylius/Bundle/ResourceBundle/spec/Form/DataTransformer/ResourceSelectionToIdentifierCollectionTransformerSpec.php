<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceSelectionToIdentifierCollectionTransformer;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class ResourceSelectionToIdentifierCollectionTransformerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([], false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceSelectionToIdentifierCollectionTransformer::class);
    }

    function it_does_transform_null_value()
    {
        $this->transform(null)->shouldReturn([]);
    }

    function it_does_not_transform_string_value()
    {
        $this->shouldThrow(UnexpectedTypeException::class)->duringTransform('');
    }

    function it_does_transform_collection_object_value(Collection $collection)
    {
        $collection->toArray()->willReturn([]);

        $this->transform($collection)->shouldReturn([]);
    }

    function it_does_reverse_transform_empty_value()
    {
        $this->reverseTransform('')->shouldImplement(Collection::class);
    }

    function it_does_not_reverse_transform_string_value()
    {
        $this->shouldThrow(UnexpectedTypeException::class)->duringReverseTransform('string');
    }

    function it_does_reverse_transform_array_value(ResourceInterface $entity)
    {
        $entity->getId()->willReturn(1);

        $this->reverseTransform([$entity])->shouldHaveCount(1);
    }

    function it_does_reverse_transform_array_of_arrays_value(ResourceInterface $entityOne, ResourceInterface $entityTwo)
    {
        $entityOne->getId()->willReturn(1);
        $entityTwo->getId()->willReturn(1);

        $this->reverseTransform([[$entityOne, $entityTwo]])->shouldHaveCount(2);
    }
}
