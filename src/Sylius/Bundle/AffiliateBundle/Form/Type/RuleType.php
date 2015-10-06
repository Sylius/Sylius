<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\Form\Type;

use Sylius\Bundle\AffiliateBundle\Form\Type\Core\AbstractConfigurationType;
use Sylius\Bundle\AffiliateBundle\Form\EventListener\BuildRuleFormSubscriber;
use Sylius\Component\Affiliate\Model\RuleInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RuleType extends AbstractConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('type', 'sylius_affiliate_goal_rule_choice', array(
                'label' => 'sylius.form.rule.type',
                'attr' => array(
                    'data-form-collection' => 'update',
                ),
            ))
            ->addEventSubscriber(
                new BuildRuleFormSubscriber($this->registry, $builder->getFormFactory(), $options['configuration_type'])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'configuration_type' => RuleInterface::TYPE_NTH_ORDER,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_goal_rule';
    }
}
