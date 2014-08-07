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
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->addEventSubscriber(new BuildActionFormListener($this->actionRegistry, $builder->getFormFactory()))
            ->add('type', 'sylius_promotion_action_choice', array(
                'label' => 'sylius.form.action.type'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action';
    }
}
