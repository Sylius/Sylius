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

use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildActionFormListener;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Promotion action form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ActionType extends AbstractResourceType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $actionRegistry;

    public function __construct($dataClass, array $validationGroups, ServiceRegistryInterface $actionRegistry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->actionRegistry = $actionRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'sylius_promotion_action_choice', array(
                'label' => 'sylius.form.action.type',
                'attr' => array(
                    'data-form-collection' => 'update'
                ),
            ))
            ->addEventSubscriber(
                new BuildActionFormListener($this->actionRegistry, $builder->getFormFactory(), $options['action_type'])
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
            'action_type',
        ));

        $resolver ->setDefaults(array(
            'action_type' => ActionInterface::TYPE_FIXED_DISCOUNT,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action';
    }
}
