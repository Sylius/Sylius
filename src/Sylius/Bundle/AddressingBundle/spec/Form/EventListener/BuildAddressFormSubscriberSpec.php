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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AddressingBundle\Form\EventListener\BuildAddressFormSubscriber;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class BuildAddressFormSubscriberSpec extends ObjectBehavior
{
    function let(RepositoryInterface $countryRepository, FormFactoryInterface $formFactory)
    {
        $this->beConstructedWith($countryRepository, $formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BuildAddressFormSubscriber::class);
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

    function it_adds_provinces_on_pre_set_data(
        FormFactoryInterface $formFactory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $provinceForm,
        AddressInterface $address,
        CountryInterface $country,
        RepositoryInterface $countryRepository
    ) {
        $event->getData()->willReturn($address);
        $event->getForm()->willReturn($form);

        $address->getCountryCode()->willReturn('IE');
        $address->getProvinceCode()->willReturn('province');

        $countryRepository->findOneBy(['code' => 'IE'])->willReturn($country);
        $country->hasProvinces()->willReturn(true);

        $formFactory
            ->createNamed('provinceCode', ProvinceCodeChoiceType::class, 'province', Argument::withKey('country'))
            ->willReturn($provinceForm);

        $form->add($provinceForm)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_province_name_field_on_pre_set_data_if_country_does_not_have_provinces(
        FormFactoryInterface $formFactory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $provinceForm,
        AddressInterface $address,
        CountryInterface $country,
        RepositoryInterface $countryRepository
    ) {
        $event->getData()->willReturn($address);
        $event->getForm()->willReturn($form);

        $address->getCountryCode()->willReturn('US');
        $address->getProvinceName()->willReturn('Utah');

        $countryRepository->findOneBy(['code' => 'US'])->willReturn($country);
        $country->hasProvinces()->willReturn(false);

        $formFactory
            ->createNamed('provinceName', TextType::class, 'Utah', Argument::any())
            ->willReturn($provinceForm);

        $form->add($provinceForm)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_provinces_on_pre_submit(
        FormFactoryInterface $formFactory,
        RepositoryInterface $countryRepository,
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

        $formFactory
            ->createNamed('provinceCode', ProvinceCodeChoiceType::class, null, Argument::withKey('country'))
            ->willReturn($provinceForm);

        $form->add($provinceForm)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_adds_province_name_field_on_pre_submit_if_country_does_not_have_provinces(
        FormFactoryInterface $formFactory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $provinceForm,
        CountryInterface $country,
        RepositoryInterface $countryRepository
    ) {
        $event->getData()->willReturn([
            'countryCode' => 'US',
        ]);
        $event->getForm()->willReturn($form);

        $countryRepository->findOneBy(['code' => 'US'])->willReturn($country);
        $country->hasProvinces()->willReturn(false);

        $formFactory
            ->createNamed('provinceName', TextType::class, null, Argument::any())
            ->willReturn($provinceForm);

        $form->add($provinceForm)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
