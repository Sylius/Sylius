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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CountryChoiceType extends AbstractType
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
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    if (null === $options['enabled']) {
                        return $this->countryRepository->findAll();
                    }

                    return $this->countryRepository->findBy(['enabled' => $options['enabled']]);
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'enabled' => true,
                'label' => 'sylius.form.address.country',
                'empty_value' => 'sylius.form.country.select',
                'choices_as_values' => true,
            ])
        ;
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
    public function getName()
    {
        return 'sylius_country_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_country_choice';
    }
}
