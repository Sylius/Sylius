<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class SetCustomerFormSubscriberSpec extends ObjectBehavior
{
    function let(RepositoryInterface $customerRepository)
    {
        $this->beConstructedWith($customerRepository);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\EventSubscriber\SetCustomerFormSubscriber');
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_submit()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SUBMIT => 'preSubmit']);
    }

    function it_adds_customer_from_database(
        FormEvent $event, 
        RepositoryInterface $customerRepository, 
        CustomerInterface $customer, FormInterface $form
    ) {
        $event->getData()->willReturn(['email' => 'imno@example.com']);
        $customerRepository->findOneBy(['email' => 'imno@example.com'])->willReturn($customer);
        $customer->getUser()->willReturn(null);
        
        $event->getForm()->willReturn($form);

        $form->setData($customer)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_does_not_add_customer_if_they_not_exist(
        FormEvent $event, 
        RepositoryInterface $customerRepository, 
        FormInterface $form
    ) {
        $event->getData()->willReturn(['email' => 'imno@example.com']);
        $customerRepository->findOneBy(['email' => 'imno@example.com'])->willReturn(null);
        
        $form->setData(null)->shouldNotBeCalled();

        $this->preSubmit($event);
    }
}
