<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Pricing;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Channel based pricing configuration form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ChannelBasedConfigurationType extends AbstractType
{
    protected $channelRepository;

    /**
     * @param RepositoryInterface $channelRepository
     */
    public function __construct(RepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->channelRepository->findAll() as $channel) {
            $builder
                ->add($channel->getId(), 'sylius_money', array(
                    'label'    => $channel->getName(),
                    'required' => false,
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price_calculator_channel_based';
    }
}
