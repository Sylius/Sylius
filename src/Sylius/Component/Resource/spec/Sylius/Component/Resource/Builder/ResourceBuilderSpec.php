<?php

namespace spec\Sylius\Component\Resource\Builder;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ResourceBuilderSpec extends ObjectBehavior
{
    function let(ObjectManager $objectManager, RepositoryInterface $objectRepository, MyModel $model)
    {
        $objectRepository->createNew()->willReturn($model);

        $this->beConstructedWith($objectManager, $objectRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Builder\ResourceBuilder');
    }

    function it_creates_resource()
    {
        $this->create()->shouldReturn($this);
    }

    function it_has_resource($model)
    {
        $this->get()->shouldReturn($model);
    }

    function it_has_no_resource_by_default()
    {
        $this->get()->shouldReturn(null);
    }

    function it_persists_resource($objectManager, $model)
    {
        $objectManager->persist($model)->shouldBeCalled();
        $this->save(false)->shouldReturn($model);
    }

    function it_flushes_resource($objectManager, $model)
    {
        $objectManager->persist($model)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();
        $this->save()->shouldReturn($model);
    }
}

class MyModel
{
}