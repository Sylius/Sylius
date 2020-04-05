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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ChannelDefaultLocaleEnabledValidator extends ConstraintValidator
{
    public function validate($channel, Constraint $constraint): void
    {
        /** @var ChannelDefaultLocaleEnabled $constraint */
        Assert::isInstanceOf($constraint, ChannelDefaultLocaleEnabled::class);

        /** @var ChannelInterface $channel */
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $defaultLocale = $channel->getDefaultLocale();
        if ($defaultLocale !== null && !$channel->hasLocale($defaultLocale)) {
            $this->context->buildViolation($constraint->message)->atPath('defaultLocale')->addViolation();
        }
    }
}
