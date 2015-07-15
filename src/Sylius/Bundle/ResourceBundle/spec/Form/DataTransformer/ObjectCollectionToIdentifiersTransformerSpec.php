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

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

// Since the root namespace "spec" is not in our autoload
require_once __DIR__.DIRECTORY_SEPARATOR.'FakeEntity.php';

/**
 * @author Liverbool <nukboon@gmail.com>
 */
class ObjectCollectionToIdentifiersTransformerSpec extends ObjectBehavior
{
    public function let(ObjectRepository $repository)
    {
        $repository->getClassName()->willReturn('spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer\FakeEntity');
        $this->beConstructedWith($repository, 'id');
    }

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectCollectionToIdentifiersTransformer');
    }

    public function it_does_transform_none_array_value()
    {
        $this->transform(Argument::any())->shouldReturn(array());
    }

    public function it_does_transform_array_of_objects(FakeEntity $entityOne, FakeEntity $entityTwo)
    {
        $entityOne->getId()->willReturn(1);
        $entityTwo->getId()->willReturn(2);

        $this->transform(array($entityOne, $entityTwo))->shouldReturn(array(1, 2));
    }

    public function it_does_reverse_transform_empty_value()
    {
        $this->reverseTransform('')->shouldReturn(array());
    }

    public function it_does_reverse_transform_identifiers_to_array_of_entities(ObjectRepository $repository, FakeEntity $entityOne, FakeEntity $entityTwo)
    {
        $value = array(1, 2);

        $entityOne->getId()->willReturn(1);
        $entityTwo->getId()->willReturn(2);

        $repository->findBy(array('id' => $value))->shouldBeCalled()->willReturn(array($entityOne, $entityTwo));

        $this->reverseTransform($value)->shouldReturn(array($entityOne, $entityTwo));
    }
}
