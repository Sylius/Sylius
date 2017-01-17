<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\EventListener;

use Doctrine\Common\Persistence\ObjectRepository;
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
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jan Góralski <jan.goralski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class BuildAddressFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectRepository
     */
    private $countryRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param ObjectRepository     $countryRepository
     * @param FormFactoryInterface $factory
     */
    public function __construct(ObjectRepository $countryRepository, FormFactoryInterface $factory)
    {
        $this->countryRepository = $countryRepository;
        $this->formFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /* @var AddressInterface $address */
        $address = $event->getData();
        if (null === $address) {
            return;
        }

        $countryCode = $address->getCountryCode();
        if (null === $countryCode) {
            return;
        }

        /* @var CountryInterface $country */
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

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('countryCode', $data)) {
            return;
        }

        if ('' === $data['countryCode']) {
            return;
        }

        /* @var CountryInterface $country */
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

    /**
     * @param CountryInterface $country
     * @param string|null $provinceCode
     *
     * @return FormInterface
     */
    private function createProvinceCodeChoiceForm(CountryInterface $country, $provinceCode = null)
    {
        return $this->formFactory->createNamed('provinceCode', ProvinceCodeChoiceType::class, $provinceCode, [
            'country' => $country,
            'auto_initialize' => false,
        ]);
    }

    /**
     * @param string|null $provinceName
     *
     * @return FormInterface
     */
    private function createProvinceNameTextForm($provinceName = null)
    {
        return $this->formFactory->createNamed('provinceName', TextType::class, $provinceName, [
            'required' => false,
            'auto_initialize' => false,
        ]);
    }
}
