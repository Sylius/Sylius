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

use Sylius\Bundle\CoreBundle\Form\Type\CustomerTaxCategoryChoiceType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerGroupType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerGroupTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxCategory', CustomerTaxCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => '---',
                'label' => 'sylius.form.customer_group.tax_category',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return CustomerGroupType::class;
    }
}
