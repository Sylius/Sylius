<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionActionFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PromotionActionType extends AbstractConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('type', 'sylius_promotion_action_choice', [
                'label' => 'sylius.form.promotion_action.type',
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
            ->addEventSubscriber(
                new BuildPromotionActionFormSubscriber(
                    $this->registry,
                    $builder->getFormFactory(),
                    (isset($options['configuration_type'])) ? $options['configuration_type'] : null
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_action';
    }
}
