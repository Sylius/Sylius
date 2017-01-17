<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule;

use Sylius\Bundle\CustomerBundle\Form\Type\CustomerGroupCodeChoiceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class CustomerGroupConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('group_code', CustomerGroupCodeChoiceType::class, [
                'label' => 'sylius.form.promotion_rule.customer_group.group',
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                    new Type(['type' => 'string', 'groups' => ['sylius']]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_rule_customer_group_configuration';
    }
}
