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

use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ChannelCodeCollectionValidator extends ConstraintValidator
{
    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ChannelCodeCollection) {
            throw new UnexpectedTypeException($constraint, ChannelCodeCollection::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        if ($constraint->validateAgainstAllChannels) {
            $this->validateInChannelCollection($value, $this->channelRepository->findAll(), $constraint);

            return;
        }

        $object = $this->context->getObject();

        if (null !== $constraint->channelAwarePropertyPath) {
            $object = $this->propertyAccessor->getValue($object, $constraint->channelAwarePropertyPath);
        }

        if (!$object instanceof ChannelsAwareInterface) {
            throw new \LogicException(sprintf(
                'The validated object needs to implement the %s interface when option `validateAgainstAllChannels` is set to false.',
                ChannelsAwareInterface::class,
            ));
        }

        $this->validateInChannelCollection($value, $object->getChannels()->toArray(), $constraint);
    }

    /**
     * @param array<array-key, array<array-key, mixed>> $value
     * @param array<BaseChannelInterface> $channels
     */
    private function validateInChannelCollection(
        array $value,
        array $channels,
        ChannelCodeCollection $constraint,
    ): void {
        $fields = [];
        foreach ($channels as $channel) {
            $fields[$channel->getCode()] = $constraint->constraints;
        }
        if ([] === $fields) {
            return;
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
