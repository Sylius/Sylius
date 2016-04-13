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

use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildRuleFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType;
use Sylius\Component\Promotion\Checker\ItemTotalRuleChecker;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RuleType extends AbstractConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('type', 'sylius_promotion_rule_choice', [
                'label' => 'sylius.form.rule.type',
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
            ->addEventSubscriber(
                new BuildRuleFormSubscriber($this->registry, $builder->getFormFactory(), $options['configuration_type'])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'configuration_type' => ItemTotalRuleChecker::TYPE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule';
    }
}
