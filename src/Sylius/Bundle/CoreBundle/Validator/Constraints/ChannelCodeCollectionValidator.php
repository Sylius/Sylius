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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ChannelCodeCollectionValidator extends ConstraintValidator
{
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ChannelCodeCollection) {
            throw new UnexpectedTypeException($constraint, ChannelCodeCollection::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        $fields = [];
        foreach ($this->channelRepository->findAll() as $channel) {
            $fields[$channel->getCode()] = $constraint->constraints;
        }

        $validator = $this->context->getValidator()->inContext($this->context);
        $validator->validate($value, new Collection($fields), $constraint->groups);
    }
}
