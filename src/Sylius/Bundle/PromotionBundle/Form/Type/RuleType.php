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

use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildRuleFormListener;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Promotion rule form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleType extends AbstractResourceType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $checkerRegistry;

    public function __construct($dataClass, array $validationGroups, ServiceRegistryInterface $checkerRegistry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->checkerRegistry = $checkerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'sylius_promotion_rule_choice', array(
                'label' => 'sylius.form.rule.type',
                'attr' => array(
                    'data-form-collection' => 'update'
                ),
            ))
            ->addEventSubscriber(
                new BuildRuleFormListener($this->checkerRegistry, $builder->getFormFactory(), $options['rule_type'])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setOptional(array(
            'rule_type',
        ));

        $resolver->setDefaults(array(
            'rule_type' => RuleInterface::TYPE_ITEM_TOTAL,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule';
    }
}
