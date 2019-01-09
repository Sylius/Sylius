<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Sylius\Component\Core\Model\ShopBillingData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShopBillingDataType extends AbstractType
{
    /** @var string */
    private $dataClass;

    public function __construct(string $dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxId', TextType::class, [
                'label' => 'sylius.form.channel.billing_data.tax_id',
                'required' => false,
            ])
            ->add('company', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.channel.billing_data.company',
            ])
            ->add('countryCode', CountryCodeChoiceType::class, [
                'label' => 'sylius.form.channel.billing_data.country',
                'enabled' => true,
                'required' => false,
            ])
            ->add('street', TextType::class, [
                'label' => 'sylius.form.channel.billing_data.street',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'sylius.form.channel.billing_data.city',
                'required' => false,
            ])
            ->add('postcode', TextType::class, [
                'label' => 'sylius.form.channel.billing_data.postcode',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->dataClass);
    }
}
