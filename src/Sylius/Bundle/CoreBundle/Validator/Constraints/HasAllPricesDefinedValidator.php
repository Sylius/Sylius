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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class HasAllPricesDefinedValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ProductVariantInterface::class);
        Assert::isInstanceOf($constraint, HasAllPricesDefined::class);

        /** @var ProductInterface|null $product */
        $product = $value->getProduct();

        if ($product === null) {
            return;
        }

        $channels = $product->getChannels();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            /** @var ChannelPricingInterface|null $channelPricing */
            $channelPricing = $value->getChannelPricingForChannel($channel);
            if (null === $channelPricing || null === $channelPricing->getPrice()) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('channelPricings')
                    ->addViolation()
                ;

                return;
            }
        }
    }
}
