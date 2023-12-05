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
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ChannelCodeCollectionValidator extends ConstraintValidator
{
    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
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

        $collection = new Collection(
            $fields,
            $constraint->groups,
            $constraint->payload,
            $constraint->allowExtraFields,
            $constraint->allowMissingFields,
            $constraint->extraFieldsMessage,
            $constraint->missingFieldsMessage,
        );
        $validator = $this->context->getValidator()->inContext($this->context);
        $validator->validate($value, $collection, $constraint->groups);
    }
}
