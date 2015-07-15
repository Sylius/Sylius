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
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class BuildAddressFormSubscriberSpec extends ObjectBehavior
{
    function let(ObjectRepository $countryRepository, FormFactoryInterface $factory)
    {
        $this->beConstructedWith($countryRepository, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\EventListener\BuildAddressFormSubscriber');
    }

    function it_is_a_subscriber()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subsribesto_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        ));
    }

    function it_adds_or_removes_provinces_on_pre_set_data(
        $factory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $provinceForm,
        AddressInterface $address,
        CountryInterface $country,
        ProvinceInterface $province
    ) {
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $event->getData()->shouldBeCalled()->willReturn($address);
        $address->getCountry()->shouldBeCalled()->willReturn($country);
        $country->hasProvinces()->shouldBeCalled()->willReturn(true);
        $address->getProvince()->shouldBeCalled()->willReturn($province);

        $factory->createNamed('province', 'sylius_province_choice', $province, Argument::withKey('country'))
            ->shouldBeCalled()
            ->willReturn($provinceForm);

        $form->add($provinceForm)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_or_removes_provinces_on_pre_submit(
        $factory,
        $countryRepository,
        FormEvent $event,
        FormInterface $form,
        FormInterface $provinceForm,
        ProvinceInterface $province,
        CountryInterface $country
    ) {
        $event->getForm()->shouldBeCalled()->willReturn($form);
        $event->getData()->shouldBeCalled()->willReturn(array(
            'country' => 'France'
        ));

        $countryRepository->find('France')->shouldBeCalled()->willReturn($country);
        $country->hasProvinces()->willReturn(true);

        $factory->createNamed('province', 'sylius_province_choice', null, Argument::withKey('country'))
            ->shouldBeCalled()
            ->willReturn($provinceForm);

        $form->add($provinceForm)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
