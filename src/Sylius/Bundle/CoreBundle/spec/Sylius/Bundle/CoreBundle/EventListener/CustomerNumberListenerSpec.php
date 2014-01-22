<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Generator\CustomerNumberGeneratorInterface;
use Sylius\Bundle\CoreBundle\Model\UserInterface;
use Symfony\Component\Form\FormInterface;

class CustomerNumberListenerSpec extends ObjectBehavior
{
    function let(CustomerNumberGeneratorInterface $generator)
    {
        $this->beConstructedWith($generator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\CustomerNumberListener');
    }

    function it_should_delegate_event_properly(FormEvent $event, FormInterface $form, UserInterface $user, $generator)
    {
        $form->getData()->willReturn($user);
        $event->getForm()->willReturn($form);

        $generator->generate($user)->shouldBeCalled();

        $this->handleEvent($event);
    }
}
