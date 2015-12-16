<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ArchetypeBundle\Form\EventListener;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ParentArchetypeListenerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('product');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ArchetypeBundle\Form\EventListener\ParentArchetypeListener');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_is_subscribed_to_pre_set_data_form_event()
    {
        $this->getSubscribedEvents()->shouldReturn(array(FormEvents::PRE_SET_DATA => 'preSetData'));
    }

    function it_throws_exception_if_add_event_subscriber_parameter_is_not_an_instance_of_archetype_interface(FormEvent $event)
    {
        $event->getData()->willReturn('badObject');
        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Archetype\Model\ArchetypeInterface'))
             ->during('preSetData', array($event))
        ;
    }
}
