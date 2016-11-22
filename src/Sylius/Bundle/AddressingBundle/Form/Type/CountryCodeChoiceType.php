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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CountryCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $countryRepository;

    /**
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
            if (null === $options['enabled']) {
                $countries = $this->countryRepository->findAll();
            } else {
                $countries = $this->countryRepository->findBy(['enabled' => $options['enabled']]);
            }

            $countryCodes = [];

            /* @var CountryInterface $country */
            foreach ($countries as $country) {
                $countryName = Intl::getRegionBundle()->getCountryName($country->getCode());
                $countryCodes[$countryName] = $country->getCode();
            }

            ksort($countryCodes);

            return $countryCodes;
        };

        $resolver->setDefaults([
            'choices' => $choices,
            'enabled' => true,
            'label' => 'sylius.form.address.country',
            'placeholder' => 'sylius.form.country.select',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_country_code_choice';
    }
}
