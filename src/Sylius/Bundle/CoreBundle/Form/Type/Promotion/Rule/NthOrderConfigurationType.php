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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class NthOrderConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nth', IntegerType::class, [
                'label' => 'sylius.form.promotion_rule.nth_order_configuration.nth',
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                    new Type(['type' => 'numeric', 'groups' => ['sylius']]),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_rule_nth_order_configuration';
    }
}
