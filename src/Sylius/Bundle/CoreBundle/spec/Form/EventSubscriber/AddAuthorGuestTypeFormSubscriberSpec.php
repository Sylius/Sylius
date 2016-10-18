<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddAuthorGuestTypeFormSubscriber;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddAuthorGuestTypeFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddAuthorGuestTypeFormSubscriber::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_set_data_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_adds_author_guest_form_type_if_user_is_not_logged_in_and_review_subject_does_not_have_author(
        FormEvent $event,
        FormInterface $form,
        ReviewInterface $review,
        FormConfigInterface $formConfig
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($review);
        $review->getAuthor()->willReturn(null);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('author')->willReturn(null);
        $form->add('author', 'sylius_customer_guest')->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_does_not_add_author_guest_form_type_if_user_is_logged_in(
        FormEvent $event,
        FormInterface $form,
        ReviewInterface $review,
        FormConfigInterface $formConfig,
        ReviewerInterface $author
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($review);
        $review->getAuthor()->willReturn(null);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('author')->willReturn($author);
        $form->add('author', 'sylius_customer_guest')->shouldNotBeCalled();

        $this->preSetData($event);
    }

    function it_does_not_add_author_guest_form_type_if_review_has_author(
        FormEvent $event,
        FormInterface $form,
        ReviewInterface $review,
        FormConfigInterface $formConfig,
        ReviewerInterface $author
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($review);
        $review->getAuthor()->willReturn($author);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('author')->willReturn(null);
        $form->add('author', 'sylius_customer_guest')->shouldNotBeCalled();

        $this->preSetData($event);
    }

    function it_throws_invalid_argument_exception_if_resource_is_not_customer_aware(
        FormEvent $event,
        FormInterface $form,
        ResourceInterface $resource,
        FormConfigInterface $formConfig
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($resource);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('author')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('preSetData', [$event]);
    }
}
