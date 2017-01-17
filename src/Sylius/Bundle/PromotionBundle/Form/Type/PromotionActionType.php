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
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class PromotionActionType extends AbstractResourceType
{
    /**
     * @var EventSubscriberInterface
     */
    private $buildActionSubscriber;

    /**
     * {@inheritdoc}
     *
     * @param EventSubscriberInterface $buildActionSubscriber
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        EventSubscriberInterface $buildActionSubscriber
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->buildActionSubscriber = $buildActionSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('type', PromotionActionChoiceType::class, [
                'label' => 'sylius.form.promotion_action.type',
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
            ->addEventSubscriber($this->buildActionSubscriber)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'configuration_type' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_action';
    }
}
