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

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ProductVariantType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!array_key_exists('onHand', $data)) {
                $form = $event->getForm();
                $form->remove('onHand');
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return BaseProductVariantType::class;
    }
}
