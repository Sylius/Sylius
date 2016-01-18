<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\EventListener;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class BuildAddressFormSubscriberSpec extends ObjectBehavior
{
    function let(ObjectRepository $countryRepository, FormFactoryInterface $formFactory)
    {
        $this->beConstructedWith($countryRepository, $formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\EventListener\BuildAddressFormSubscriber');
    }

    function it_is_a_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        ));
    }

    function it_adds_or_removes_administrative_areas_on_pre_set_data(
        FormFactoryInterface $formFactory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $administrativeAreaForm,
        AddressInterface $address,
        CountryInterface $country,
        AdministrativeAreaInterface $administrativeArea
    ) {
        $event->getForm()->willReturn($form);

        $event->getData()->willReturn($address);
        $country->getCode()->willReturn('IE');
        $address->getCountry()->willReturn('IE');
        $country->hasAdministrativeAreas()->willReturn(true);
        $administrativeArea->getCode()->willReturn('TB');
        $address->getAdministrativeArea()->willReturn('TB');

        $formFactory->createNamed('administrative_area', 'sylius_administrative_area_code_choice', 'TB', Argument::withKey('country'))
            ->willReturn($administrativeAreaForm)
        ;

        $this->preSetData($event);
    }

    function it_adds_or_removes_administrative_areas_on_pre_submit(
        FormFactoryInterface $formFactory,
        ObjectRepository $countryRepository,
        FormEvent $event,
        FormInterface $form,
        FormInterface $administrativeAreaForm,
        CountryInterface $country
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn(array(
            'country' => 'FR'
        ));

        $countryRepository->findOneBy(array('code' => 'FR'))->willReturn($country);
        $country->hasAdministrativeAreas()->willReturn(true);

        $formFactory->createNamed('administrative_area', 'sylius_administrative_area_code_choice', null, Argument::withKey('country'))
            ->willReturn($administrativeAreaForm)
        ;

        $form->add($administrativeAreaForm)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
