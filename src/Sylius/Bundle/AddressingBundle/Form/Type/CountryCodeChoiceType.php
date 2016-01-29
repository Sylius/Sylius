<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CountryCodeChoiceType extends CountryChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $choices = function (Options $options) {
            if (null === $options['enabled']) {
                $countries = $this->countryRepository->findAll();
            } else {
                $countries = $this->countryRepository->findBy(['enabled' => $options['enabled']]);
            }

            return $this->getCountryCodes($countries);
        };

        $resolver->setDefault('choice_list', null);
        $resolver->setDefault('choices', $choices);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_country_code_choice';
    }

    /**
     * @param CountryInterface[] $countries
     *
     * @return array
     */
    private function getCountryCodes(array $countries)
    {
        $countryCodes = [];

        /* @var CountryInterface $country */
        foreach ($countries as $country) {
            $countryCodes[$country->getCode()] = Intl::getRegionBundle()->getCountryName($country->getCode());
        }

        return $countryCodes;
    }
}
