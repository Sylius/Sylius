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
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

// Since the root namespace "spec" is not in our autoload
require_once __DIR__ . DIRECTORY_SEPARATOR . 'FakeEntity.php';

/**
 * @author Liverbool <nukboon@gmail.com>
 */
class ResourceCollectionToIdentifiersTransformerSpec extends ObjectBehavior
{
    function let(ResourceRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'id');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceCollectionToIdentifiersTransformer');
    }

    function it_does_transform_none_array_value()
    {
        $this->transform(Argument::any())->shouldReturn(array());
    }

    function it_does_transform_array_of_objects(FakeEntity $resourceOne, FakeEntity $resourceTwo)
    {
        $resourceOne->getId()->willReturn(1);
        $resourceTwo->getId()->willReturn(2);

        $this->transform(array($resourceOne, $resourceTwo))->shouldReturn(array(1, 2));
    }

    function it_does_reverse_transform_empty_value()
    {
        $this->reverseTransform('')->shouldReturn(array());
    }

    function it_does_reverse_transform_identifiers_to_array_of_entities($repository, FakeEntity $resourceOne, FakeEntity $resourceTwo)
    {
        $value = array(1, 2);

        $resourceOne->getId()->willReturn(1);
        $resourceTwo->getId()->willReturn(2);

        $repository->findBy(array('id' => $value))->shouldBeCalled()->willReturn(array($resourceOne, $resourceTwo));

        $this->reverseTransform($value)->shouldReturn(array($resourceOne, $resourceTwo));
    }
}
