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

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as BaseAddressType;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Repository\CountryRepositoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

final class AddressType extends AbstractType
{
    /**
     * @param CountryRepositoryInterface<CountryInterface> $countryRepository
     */
    public function __construct(private readonly CountryRepositoryInterface $countryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->addDependent('provinceCode', 'countryCode', function (DependentField $field, ?string $countryCode = null) {
                if (null === $countryCode) {
                    return;
                }

                $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

                if ($country->hasProvinces()) {
                    $field->add(ProvinceCodeChoiceType::class, [
                        'country' => $country,
                        'placeholder' => 'sylius.form.province.select',
                        'label' => 'sylius.form.address.province',
                        'auto_initialize' => false,
                    ]);
                }
            })
            ->addDependent('provinceName', 'countryCode', function (DependentField $field, ?string $countryCode = null) {
                if (null === $countryCode) {
                    return;
                }

                $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

                if (!$country->hasProvinces()) {
                    $field->add(TextType::class, [
                        'label' => 'sylius.form.address.province',
                        'required' => false,
                        'auto_initialize' => false,
                    ]);
                }
            });

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $formEvent) {
                /** @var AddressInterface $data */
                $data = $formEvent->getData();
                $form = $formEvent->getForm();

                $form->has('provinceCode') ?: $data->setProvinceCode(null);
                $form->has('provinceName') ?: $data->setProvinceName(null);
            },
        );
    }

    public function getParent(): string
    {
        return BaseAddressType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_address';
    }
}
