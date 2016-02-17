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
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds the province field to form if needed.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class BuildAddressFormSubscriber implements EventSubscriberInterface
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
     * Removes or adds a province field based on the country set.
     *
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

        if ($country->hasProvinces()) {
            $event->getForm()->add($this->createProvinceCodeChoiceForm($country, $address->getProvinceCode()));
        }
    }

    /**
     * Removes or adds a province field based on the country set on submitted form.
     *
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

        if ($country->hasProvinces()) {
            $event->getForm()->add($this->createProvinceCodeChoiceForm($country));
        }
    }

    /**
     * @param CountryInterface $country
     * @param string|null $provinceCode
     *
     * @return FormInterface
     */
    private function createProvinceCodeChoiceForm(CountryInterface $country, $provinceCode = null)
    {
        return
            $this
                ->formFactory
                    ->createNamed('provinceCode', 'sylius_province_code_choice', $provinceCode, [
                    'country' => $country,
                    'auto_initialize' => false,
            ])
        ;
    }
}
