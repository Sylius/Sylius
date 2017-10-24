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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryType;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Intl;

final class CountryTypeExtension extends AbstractTypeExtension
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * {@inheritdoc}
     */
    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
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
            ->add('provinces', CollectionType::class, [
                'entry_type' => ProvinceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_label' => 'sylius.form.country.add_province',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.form.country.enabled',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return CountryType::class;
    }

    /**
     * @param string $code
     *
     * @return string|null
     */
    private function getCountryName(string $code): ?string
    {
        return Intl::getRegionBundle()->getCountryName($code);
    }

    /**
     * @return array|CountryInterface[]
     */
    private function getAvailableCountries(): array
    {
        $availableCountries = Intl::getRegionBundle()->getCountryNames();

        /** @var CountryInterface[] $definedCountries */
        $definedCountries = $this->countryRepository->findAll();

        foreach ($definedCountries as $country) {
            unset($availableCountries[$country->getCode()]);
        }

        return $availableCountries;
    }
}
