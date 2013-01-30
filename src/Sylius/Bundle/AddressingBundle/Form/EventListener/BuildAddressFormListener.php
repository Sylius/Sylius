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
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * This listener adds the province field to form if needed.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BuildAddressFormListener implements EventSubscriberInterface
{
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
            FormEvents::PRE_BIND     => 'preBind'
        );
    }

    /**
     * Removes or adds a province field based on the country set.
     *
     * @param DataEvent $event
     */
    public function preSetData(DataEvent $event)
    {
        $address = $event->getData();
        $form = $event->getForm();

        if (null === $address) {
            return;
        }

        $country = $address->getCountry();

        if (null === $country) {
            return;
        }

        if ($country->hasProvinces()) {
            $form->add($this->factory->createNamed('province', 'sylius_province_choice', $address->getProvince(), array(
                'country'  => $country
            )));
        }
    }

    /**
     * Removes or adds a province field based on the country set on submitted form.
     *
     * @param DataEvent $event
     */
    public function preBind(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $countryId = $data['country'];

        $country = $this->countryRepository->find($countryId);

        if ($country->hasProvinces()) {
            $form->add($this->factory->createNamed('province', 'sylius_province_choice', null, array(
                'country'  => $country
            )));
        }
    }
}
