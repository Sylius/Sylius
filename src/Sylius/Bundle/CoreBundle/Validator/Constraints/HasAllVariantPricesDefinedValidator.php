<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class HasAllVariantPricesDefinedValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($product, Constraint $constraint)
    {
        Assert::isInstanceOf($product, ProductInterface::class);

        if ($product->isSimple()) {
            return;
        }

        $channels = $product->getChannels();

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $productVariant) {
            /** @var ChannelInterface $channel */
            foreach ($channels as $channel) {
                /** @var ChannelPricingInterface $channelPricing */
                $channelPricing = $productVariant->getChannelPricingForChannel($channel);
                if (null === $channelPricing || null === $channelPricing->getPrice()) {
                    $this->context->buildViolation($constraint->message)
                        ->atPath('channels')
                        ->addViolation()
                    ;

                    return;
                }
            }
        }
    }
}
