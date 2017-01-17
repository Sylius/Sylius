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

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class HasAllPricesDefinedValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($productVariant, Constraint $constraint)
    {
        Assert::isInstanceOf($constraint, HasAllPricesDefined::class);

        $channels = $productVariant->getProduct()->getChannels();

        foreach ($channels as $channel) {
            /** @var ChannelPricingInterface $channelPricing */
            $channelPricing = $productVariant->getChannelPricingForChannel($channel);
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
