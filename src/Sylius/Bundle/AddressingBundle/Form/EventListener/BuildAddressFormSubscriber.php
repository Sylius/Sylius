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
use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds the administrative area field to form if needed.
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
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(ObjectRepository $countryRepository, FormFactoryInterface $formFactory)
    {
        $this->countryRepository = $countryRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit',
        );
    }

    /**
     * Removes or adds an administrative area field based on the country set.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $address = $event->getData();
        if (null === $address) {
            return;
        }

        $countryCode = $address->getCountry();
        if (null === $countryCode) {
            return;
        }

        /* @var CountryInterface $country */
        $country = $this->countryRepository->findOneBy(array('code' => $countryCode));
        if (null === $country) {
            return;
        }

        if ($country->hasAdministrativeAreas()) {
            $event->getForm()->add($this->createAdministrativeAreaCodeChoiceForm($country, $address->getAdministrativeArea()));
        }
    }

    /**
     * Removes or adds an administrative area field based on the country set on submitted form.
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('country', $data)) {
            return;
        }

        if ('' === $data['country']) {
            return;
        }

        /* @var CountryInterface $country */
        $country = $this->countryRepository->findOneBy(array('code' => $data['country']));
        if (null === $country) {
            return;
        }

        if ($country->hasAdministrativeAreas()) {
            $event->getForm()->add($this->createAdministrativeAreaCodeChoiceForm($country));
        }
    }

    /**
     * @param CountryInterface $country
     * @param AdministrativeAreaInterface|null $administrativeArea
     *
     * @return FormInterface
     */
    private function createAdministrativeAreaCodeChoiceForm(CountryInterface $country, AdministrativeAreaInterface $administrativeArea = null)
    {
        return
            $this
                ->formFactory
                    ->createNamed('administrative_area', 'sylius_administrative_area_code_choice', $administrativeArea, array(
                    'country'  => $country,
                    'auto_initialize' => false,
            ))
        ;
    }
}
