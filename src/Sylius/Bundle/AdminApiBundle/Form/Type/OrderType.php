<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminApiBundle\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Validator\Constraints\NotBlank;

final class OrderType extends AbstractResourceType
{
    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var ?ChoiceLoaderInterface */
    private $customerChoiceLoader;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        string $dataClass,
        array $validationGroups = [],
        RepositoryInterface $localeRepository,
        ?ChoiceLoaderInterface $customerChoiceLoader = null
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->localeRepository = $localeRepository;

        if ($customerChoiceLoader === null) {
            @trigger_error(sprintf('Not passing a $customerChoiceLoader to %s constructor is deprecated since Sylius 1.5 and will be removed in Sylius 2.0.', self::class), \E_USER_DEPRECATED);
        }

        $this->customerChoiceLoader = $customerChoiceLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', CustomerChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ],
                'choices' => [], /** Intentionally left blank, as in the only usage of this loader is in the context of api, where we don't need to load choices */
                'choice_loader' => $this->customerChoiceLoader,
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

                /** @var ChannelInterface|null $channel */
                $channel = $order->getChannel();

                if (null !== $channel) {
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
    public function getBlockPrefix(): string
    {
        return 'sylius_admin_api_order';
    }
}
