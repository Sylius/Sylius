<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\Product;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChannelPricingType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private ?\Sylius\Component\Resource\Repository\RepositoryInterface $channelPricingRepository = null,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', MoneyType::class, [
                'label' => 'sylius.ui.price',
                'currency' => $options['channel']->getBaseCurrency()->getCode(),
            ])
            ->add('originalPrice', MoneyType::class, [
                'label' => 'sylius.ui.original_price',
                'required' => false,
                'currency' => $options['channel']->getBaseCurrency()->getCode(),
            ])
            ->add('minimumPrice', MoneyType::class, [
                'label' => 'sylius.ui.minimum_price',
                'required' => false,
                'currency' => $options['channel']->getBaseCurrency()->getCode(),
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($options): void {
            $channelPricing = $event->getData();

            if (!$channelPricing instanceof $this->dataClass || !$channelPricing instanceof ChannelPricingInterface) {
                $event->setData(null);

                return;
            }

            if ($channelPricing->getPrice() === null && $channelPricing->getOriginalPrice() === null) {
                $event->setData(null);

                if ($channelPricing->getId() !== null) {
                    $this->channelPricingRepository->remove($channelPricing);
                }

                return;
            }

            $channelPricing->setChannelCode($options['channel']->getCode());
            $channelPricing->setProductVariant($options['product_variant']);

            $event->setData($channelPricing);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('channel')
            ->setAllowedTypes('channel', [ChannelInterface::class])

            ->setDefined('product_variant')
            ->setAllowedTypes('product_variant', ['null', ProductVariantInterface::class])

            ->setDefaults([
                'label' => fn (Options $options): string => $options['channel']->getName(),
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_channel_pricing';
    }
}
