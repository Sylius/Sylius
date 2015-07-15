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

// Since the root namespace "spec" is not in our autoload
require_once __DIR__.DIRECTORY_SEPARATOR.'FakeEntity.php';

class ObjectSelectionToIdentifierCollectionTransformerSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array(), false);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectSelectionToIdentifierCollectionTransformer');
    }

    public function it_does_transform_null_value()
    {
        $this->transform(null)->shouldReturn(array());
    }

    public function it_does_not_transform_string_value()
    {
        $this->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')->duringTransform('');
    }

    public function it_does_transform_collection_object_value(Collection $collection)
    {
        $collection->toArray()->willReturn(array());

        $this->transform($collection)->shouldReturn(array());
    }

    public function it_does_reverse_transform_empty_value()
    {
        $this->reverseTransform('')->shouldImplement('Doctrine\Common\Collections\Collection');
    }

    public function it_does_not_reverse_transform_string_value()
    {
        $this->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')->duringReverseTransform('string');
    }

    public function it_does_reverse_transform_array_value(FakeEntity $entity)
    {
        $entity->getId()->willReturn(1);

        $this->reverseTransform(array($entity))->shouldHaveCount(1);
    }

    public function it_does_reverse_transform_array_of_arrays_value(FakeEntity $entityOne, FakeEntity $entityTwo)
    {
        $entityOne->getId()->willReturn(1);
        $entityTwo->getId()->willReturn(1);

        $this->reverseTransform(array(array($entityOne, $entityTwo)))->shouldHaveCount(2);
    }
}
