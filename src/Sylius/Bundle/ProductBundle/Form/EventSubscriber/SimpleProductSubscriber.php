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

namespace Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

final class SimpleProductSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $product = $event->getData();

        /** @var ProductInterface $product */
        Assert::isInstanceOf($product, ProductInterface::class);

        if ($product->isSimple()) {
            $form = $event->getForm();

            $form->add('variant', ProductVariantType::class, [
                'property_path' => 'variants[0]',
                'constraints' => [
                    new Valid(),
                ],
            ]);
            $form->remove('options');
        }
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (!empty($data) && array_key_exists('variant', $data) && array_key_exists('code', $data)) {
            $data['variant']['code'] = $data['code'];
        }
        if (!empty($data) && array_key_exists('variant', $data) && array_key_exists('enabled', $data)) {
            $data['variant']['enabled'] = $data['enabled'];
        }

        $event->setData($data);
    }
}
