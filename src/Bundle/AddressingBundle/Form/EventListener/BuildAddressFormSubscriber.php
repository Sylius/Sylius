<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Form\EventListener;

use Doctrine\Persistence\ObjectRepository;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @internal
 */
final class BuildAddressFormSubscriber implements EventSubscriberInterface
{
    public function __construct(private ObjectRepository $countryRepository, private FormFactoryInterface $formFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        /** @var AddressInterface|null $address */
        $address = $event->getData();
        if (null === $address) {
            return;
        }

        $countryCode = $address->getCountryCode();
        if (null === $countryCode) {
            return;
        }

        /** @var CountryInterface|null $country */
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);
        if (null === $country) {
            return;
        }

        $form = $event->getForm();

        if ($country->hasProvinces()) {
            $form->add($this->createProvinceCodeChoiceForm($country, $address->getProvinceCode()));

            return;
        }

        $form->add($this->createProvinceNameTextForm($address->getProvinceName()));
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('countryCode', $data)) {
            return;
        }

        if ('' === $data['countryCode']) {
            return;
        }

        /** @var CountryInterface|null $country */
        $country = $this->countryRepository->findOneBy(['code' => $data['countryCode']]);
        if (null === $country) {
            return;
        }

        $form = $event->getForm();

        if ($country->hasProvinces()) {
            $form->add($this->createProvinceCodeChoiceForm($country));

            return;
        }

        $form->add($this->createProvinceNameTextForm());
    }

    private function createProvinceCodeChoiceForm(CountryInterface $country, ?string $provinceCode = null): FormInterface
    {
        return $this->formFactory->createNamed('provinceCode', ProvinceCodeChoiceType::class, $provinceCode, [
            'country' => $country,
            'auto_initialize' => false,
            'label' => 'sylius.form.address.province',
            'placeholder' => 'sylius.form.province.select',
        ]);
    }

    private function createProvinceNameTextForm(?string $provinceName = null): FormInterface
    {
        return $this->formFactory->createNamed('provinceName', TextType::class, $provinceName, [
            'required' => false,
            'auto_initialize' => false,
            'label' => 'sylius.form.address.province',
        ]);
    }
}
