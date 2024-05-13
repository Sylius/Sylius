<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryType as BaseCountryType;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Countries;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class CountryType extends AbstractType
{
    /** @param RepositoryInterface<CountryInterface> $countryRepository */
    public function __construct(private readonly RepositoryInterface $countryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $options = [
                'label' => 'sylius.form.country.name',
                'choice_loader' => null,
            ];

            $country = $event->getData();
            if ($country instanceof CountryInterface && null !== $country->getCode()) {
                $options['disabled'] = true;
                $options['choices'] = [$this->getCountryName($country->getCode()) => $country->getCode()];
            } else {
                $options['choices'] = array_flip($this->getAvailableCountries());
            }

            $form = $event->getForm();
            $form->add('code', \Symfony\Component\Form\Extension\Core\Type\CountryType::class, $options);
        });

        $builder
            ->add('provinces', LiveCollectionType::class, [
                'entry_type' => ProvinceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_options' => [
                    'label' => 'sylius.form.country.add_province',
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.form.country.enabled',
            ])
        ;
    }

    public function getParent(): string
    {
        return BaseCountryType::class;
    }

    private function getCountryName(string $code): string
    {
        return Countries::getName($code);
    }

    /** @return string[] */
    private function getAvailableCountries(): array
    {
        $availableCountries = Countries::getNames();

        /** @var CountryInterface[] $definedCountries */
        $definedCountries = $this->countryRepository->findAll();

        foreach ($definedCountries as $country) {
            unset($availableCountries[$country->getCode()]);
        }

        return $availableCountries;
    }
}
