<?php

namespace spec\Sylius\Bundle\ResourceBundle\Doctrine;

use PHPSpec2\ObjectBehavior;

/**
 * Doctrine resource manager spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceManager extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    function let($objectManager)
    {
        $this->beConstructedWith($objectManager, 'Resource');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ResourceManager');
    }

    function it_should_be_resource_manager()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface');
    }

    function it_should_return_correct_class_name()
    {
        $this->getClassName()->shouldReturn('Resource');
    }

    /**
     * @param Sylius\Bundle\ResourceBundle\Model\ResourceInterface $resource
     */
    function it_should_persist_the_resource_via_object_manager($objectManager, $resource)
    {
        $objectManager->persist($resource)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->persist($resource);
    }

    /**
     * @param Sylius\Bundle\ResourceBundle\Model\ResourceInterface $resource
     */
    function it_should_not_flush_on_persist_if_not_needed($objectManager, $resource)
    {
        $objectManager->persist($resource)->shouldBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->persist($resource, false);
    }

    /**
     * @param Sylius\Bundle\ResourceBundle\Model\ResourceInterface $resource
     */
    function it_should_remove_the_resource_via_object_manager($objectManager, $resource)
    {
        $objectManager->remove($resource)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->remove($resource);
    }

    /**
     * @param Sylius\Bundle\ResourceBundle\Model\ResourceInterface $resource
     */
    function it_should_not_flush_on_remove_if_not_needed($objectManager, $resource)
    {
        $objectManager->remove($resource)->shouldBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->remove($resource, false);
    }
}
