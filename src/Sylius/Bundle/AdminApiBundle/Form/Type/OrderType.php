<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderType extends AbstractResourceType
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * {@inheritdoc}
     *
     * RepositoryInterface $localeRepository
     */
    public function __construct($dataClass, array $validationGroups = [], RepositoryInterface $localeRepository)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', CustomerChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ],
            ])
            ->add('localeCode', LocaleChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ],
            ])
            ->add('channel', ChannelChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                /** @var OrderInterface $order */
                $order = $event->getData();

                /** @var ChannelInterface $channel */
                if (null !== $channel = $order->getChannel()) {
                    $order->setCurrencyCode($channel->getBaseCurrency()->getCode());
                }
            })
        ;

        $builder->get('localeCode')->addModelTransformer(
            new ReversedTransformer(new ResourceToIdentifierTransformer($this->localeRepository, 'code'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_admin_api_order';
    }
}
