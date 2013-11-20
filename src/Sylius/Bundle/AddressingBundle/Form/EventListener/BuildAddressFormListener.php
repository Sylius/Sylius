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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * This listener adds the province field to form if needed.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BuildAddressFormListener implements EventSubscriberInterface
{
    /**
     * @var ObjectRepository
     */
    private $countryRepository;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param ObjectRepository     $countryRepository
     * @param FormFactoryInterface $factory
     */
    public function __construct(ObjectRepository $countryRepository, FormFactoryInterface $factory)
    {
        $this->countryRepository = $countryRepository;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preBind'
        );
    }

    /**
     * Removes or adds a province field based on the country set.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $address = $event->getData();
        if (null === $address) {
            return;
        }

        $country = $address->getCountry();
        if (null === $country) {
            return;
        }

        if ($country->hasProvinces()) {
            $event->getForm()->add($this->factory->createNamed('province', 'sylius_province_choice', $address->getProvince(), array(
                'country' => $country, 'auto_initialize' => false
            )));
        }
    }

    /**
     * Removes or adds a province field based on the country set on submitted form.
     *
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('country', $data)) {
            return;
        }

        $country = $this->countryRepository->find($data['country']);
        if (null === $country) {
            return;
        }

        if ($country->hasProvinces()) {
            $event->getForm()->add($this->factory->createNamed('province', 'sylius_province_choice', null, array(
                'country'  => $country, 'auto_initialize' => false
            )));
        }
    }
}
