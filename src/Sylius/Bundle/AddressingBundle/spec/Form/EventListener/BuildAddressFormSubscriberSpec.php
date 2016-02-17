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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
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
        $this::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ]);
    }

    function it_adds_or_removes_provinces_on_pre_set_data(
        FormFactoryInterface $formFactory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $provinceForm,
        AddressInterface $address,
        CountryInterface $country,
        ProvinceInterface $province
    ) {
        $event->getForm()->willReturn($form);

        $event->getData()->willReturn($address);
        $country->getCode()->willReturn('IE');
        $address->getCountryCode()->willReturn('IE');
        $country->hasProvinces()->willReturn(true);
        $province->getCode()->willReturn('province');
        $address->getProvinceCode()->willReturn('province');

        $formFactory->createNamed('provinceCode', 'sylius_province_code_choice', 'province', Argument::withKey('country'))
            ->willReturn($provinceForm);

        $this->preSetData($event);
    }

    function it_adds_or_removes_provinces_on_pre_submit(
        FormFactoryInterface $formFactory,
        ObjectRepository $countryRepository,
        FormEvent $event,
        FormInterface $form,
        FormInterface $provinceForm,
        CountryInterface $country
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn([
            'countryCode' => 'FR',
        ]);

        $countryRepository->findOneBy(['code' => 'FR'])->willReturn($country);
        $country->hasProvinces()->willReturn(true);

        $formFactory->createNamed('provinceCode', 'sylius_province_code_choice', null, Argument::withKey('country'))
            ->willReturn($provinceForm);

        $form->add($provinceForm)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
