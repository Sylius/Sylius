<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CountryChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_filter' => null,
                'choices' => function (Options $options): iterable {
                    if (null === $options['enabled']) {
                        $countries = $this->countryRepository->findAll();
                    } else {
                        $countries = $this->countryRepository->findBy(['enabled' => $options['enabled']]);
                    }

                    if ($options['choice_filter']) {
                        $countries = array_filter($countries, $options['choice_filter']);
                    }

                    usort($countries, function (CountryInterface $a, CountryInterface $b): int {
                        return $a->getName() <=> $b->getName();
                    });

                    return $countries;
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'enabled' => true,
                'label' => 'sylius.form.address.country',
                'placeholder' => 'sylius.form.country.select',
            ])
            ->setAllowedTypes('choice_filter', ['null', 'callable'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_country_choice';
    }
}
