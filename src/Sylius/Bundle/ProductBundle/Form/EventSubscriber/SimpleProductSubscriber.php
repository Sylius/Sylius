<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class SimpleProductSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData();

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

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('variant', $data) || !array_key_exists('code', $data)) {
            return;
        }

        $data['variant']['code'] = $data['code'];

        $event->setData($data);
    }
}
