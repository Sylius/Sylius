<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion;

use Sylius\Bundle\CoreBundle\Form\EventSubscriber\BuildChannelAwarePromotionActionFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PromotionActionType extends AbstractConfigurationType
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param string $dataClass
     * @param ServiceRegistryInterface $registry
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct($dataClass, ServiceRegistryInterface $registry, ChannelRepositoryInterface $channelRepository)
    {
        parent::__construct($dataClass, $registry);

        $this->channelRepository = $channelRepository;
    }

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
                new BuildChannelAwarePromotionActionFormSubscriber(
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
    public function getName()
    {
        return 'sylius_promotion_action';
    }
}
