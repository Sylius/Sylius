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

namespace Sylius\Bundle\ShopBundle\Form\Type\Checkout;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType as BaseAddressType;
use Sylius\Bundle\ShopBundle\Form\Type\AddressType as SyliusAddressType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

final class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event): void {
                $form = $event->getForm();

                Assert::isInstanceOf($event->getData(), OrderInterface::class);

                /** @var OrderInterface $order */
                $order = $event->getData();
                $channel = $order->getChannel();

                $form
                    ->add('shippingAddress', SyliusAddressType::class, [
                        'shippable' => true,
                        'constraints' => [new Valid()],
                        'channel' => $channel,
                    ])
                    ->add('billingAddress', SyliusAddressType::class, [
                        'constraints' => [new Valid()],
                        'channel' => $channel,
                    ])
                ;
            })
        ;
    }

    public function getParent(): string
    {
        return BaseAddressType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shop_checkout_address';
    }
}
