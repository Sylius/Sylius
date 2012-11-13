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

    /**
     * @param Sylius\Bundle\ResourceBundle\Model\ResourceInterface $resource
     */
    function it_should_persist_the_resource_via_object_manager($objectManager, $resource)
    {
        $objectManager->persist($resource)->shouldBeCalled();

        $this->persist($resource);
    }

    /**
     * @param Sylius\Bundle\ResourceBundle\Model\ResourceInterface $resource
     */
    function it_should_remove_the_resource_via_object_manager($objectManager, $resource)
    {
        $objectManager->remove($resource)->shouldBeCalled();

        $this->remove($resource);
    }
}
