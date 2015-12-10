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
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CountryChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
            if (null === $options['enabled']) {
                $choices = $this->repository->findAll();
            } else {
                $choices = $this->repository->findBy(array('enabled' => $options['enabled']));
            }

            return $this->getCountryCodes($choices);
        };

        $resolver
            ->setDefaults(array(
                'choices'     => $choices,
                'enabled'     => true,
                'label'       => 'sylius.form.zone.types.country',
                'empty_value' => 'sylius.form.country.select',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_country_choice';
    }

    /**
     * @param CountryInterface[] $countries
     *
     * @return array
     */
    protected function getCountryCodes(array $countries)
    {
        $countryCodes = array();

        /* @var CountryInterface $country */
        foreach ($countries as $country){
            $countryCodes[$country->getCode()] = Intl::getRegionBundle()->getCountryName($country->getCode());
        }

        return $countryCodes;
    }
}
