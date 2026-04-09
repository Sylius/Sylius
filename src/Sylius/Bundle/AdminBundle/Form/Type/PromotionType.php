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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionType as BasePromotionType;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $promotion = $event->getData();

            if ($promotion instanceof PromotionInterface && !$promotion->isTrackUsage()) {
                $event->getForm()->add('usageLimit', IntegerType::class, [
                    'label' => 'sylius.form.promotion.usage_limit',
                    'required' => false,
                    'disabled' => true,
                ]);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $trackUsage = isset($data['trackUsage']) && (bool) $data['trackUsage'];

            $event->getForm()->add('usageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion.usage_limit',
                'required' => false,
                'disabled' => !$trackUsage,
            ]);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_promotion';
    }

    public function getParent(): string
    {
        return BasePromotionType::class;
    }
}
