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

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionConfigurationChannelCodesValidator extends ConstraintValidator
{
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, PromotionConfigurationChannelCodes::class);

        if (!is_array($value)) {
            return;
        }

        $channelCodes = $value[ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY] ?? [];

        foreach ($channelCodes as $code) {
            if (!is_string($code) || $this->channelRepository->findOneByCode($code) === null) {
                $this->context->buildViolation($constraint->invalidChannelCodeMessage)
                    ->setParameter('{{ channelCode }}', (string) $code)
                    ->atPath('[' . ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY . ']')
                    ->addViolation()
                ;
            }
        }
    }
}
