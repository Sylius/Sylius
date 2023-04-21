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

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ExistingChannelCodeValidator extends ConstraintValidator
{
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::nullOrString($value);
        Assert::isInstanceOf($constraint, ExistingChannelCode::class);

        if ($value === null) {
            return;
        }

        if ($this->channelRepository->findOneByCode($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->atPath('channelPricings')
                ->setParameter('{{ channelCode }}', $value)
                ->addViolation()
            ;
        }
    }
}
