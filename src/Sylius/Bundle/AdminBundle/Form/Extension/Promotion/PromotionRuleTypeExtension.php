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

namespace Sylius\Bundle\AdminBundle\Form\Extension\Promotion;

use Sylius\Bundle\AdminBundle\Form\Builder\ChannelExclusionFormBuilder;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleType;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class PromotionRuleTypeExtension extends AbstractTypeExtension
{
    public function __construct(private ?ChannelExclusionFormBuilder $channelExclusionFormBuilder = null)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->channelExclusionFormBuilder?->build(
            $builder,
            'sylius.form.promotion_rule.channels',
            PromotionRuleInterface::class,
        );
    }

    public static function getExtendedTypes(): iterable
    {
        yield PromotionRuleType::class;
    }
}
