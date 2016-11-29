<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\EventSubscriber\BuildChannelBasedPromotionActionFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionChoiceType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionActionTypeExtension extends AbstractTypeExtension
{
    /**
     * @var ServiceRegistryInterface
     */
    private $registry;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ServiceRegistryInterface $registry
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(ServiceRegistryInterface $registry, ChannelRepositoryInterface $channelRepository)
    {
        $this->registry = $registry;
        $this->channelRepository = $channelRepository;
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
            ->addEventSubscriber(
                new BuildChannelBasedPromotionActionFormSubscriber(
                    $this->registry,
                    $builder->getFormFactory(),
                    (isset($options['configuration_type'])) ? $options['configuration_type'] : null,
                    $this->channelRepository
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PromotionActionType::class;
    }
}
