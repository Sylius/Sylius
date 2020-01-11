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

namespace Sylius\Bundle\TaxationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxRateType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'sylius.form.tax_rate.name',
            ])
            ->add('category', TaxCategoryChoiceType::class, [
                'label' => 'sylius.form.tax_rate.category',
            ])
            ->add('calculator', TaxCalculatorChoiceType::class, [
                'label' => 'sylius.form.tax_rate.calculator',
            ])
            ->add('amount', PercentType::class, [
                'label' => 'sylius.form.tax_rate.amount',
                'scale' => 3,
            ])
            ->add('includedInPrice', CheckboxType::class, [
                'label' => 'sylius.form.tax_rate.included_in_price',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_tax_rate';
    }
}
