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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

// Since the root namespace "spec" is not in our autoload
require_once __DIR__ . DIRECTORY_SEPARATOR . 'FakeEntity.php';

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ResourceAutocompleteToIdentifierTransformerSpec extends ObjectBehavior
{
    function let()
    {
       $this->beConstructedWith(array('select' => 'name'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(
            'Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceAutocompleteToIdentifierTransformer'
        );
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_resource_to_array_data(FakeEntity $entity)
    {
        $entity->getName()->willReturn('name');
        $this->transform($entity)->shouldReturn(array('select' => 'name', 'resource' => $entity));
    }

    function it_reverse_transforms_array_data_to_resource(FakeEntity $entity)
    {
        $this->reverseTransform(array('select' => 'name', 'resource' => $entity))->shouldReturn($entity);
    }

    function it_does_not_transform_null_value()
    {
        $this->transform(null)->shouldReturn(array());
    }

    function it_does_not_reverse_null_value()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }
}
