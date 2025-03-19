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
    /** @var array<string, string> */
    private array $channelsCache = [];

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
            $this->validateInChannelCollection($value, array_keys($this->getAllChannelsCodes()), $constraint);

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

        $channelCodes = $this->getApplicableChannels($object, $value);

        $this->validateInChannelCollection($value, $channelCodes, $constraint);
    }

    /**
     * @param array<array-key, array<array-key, mixed>> $value
     * @param string[] $channels
     */
    private function validateInChannelCollection(
        array $value,
        array $channels,
        ChannelCodeCollection $constraint,
    ): void {
        $existingChannels = $this->getAllChannelsCodes();

        $fields = [];
        foreach ($channels as $channel) {
            if (!isset($existingChannels[$channel])) {
                $this->context->buildViolation($constraint->invalidChannelMessage)
                    ->setParameter('{{ channel_code }}', $channel)
                    ->addViolation()
                ;

                continue;
            }

            $fields[$channel] = $constraint->constraints;
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

    /** @return array<string, mixed> */
    private function getAllChannelsCodes(): array
    {
        if ([] !== $this->channelsCache) {
            return $this->channelsCache;
        }

        /** @var array{code: string} $channelsData */
        $channelsData = $this->channelRepository->findAllWithBasicData();

        $this->channelsCache = array_flip(array_column($channelsData, 'code'));

        return $this->channelsCache;
    }

    /**
     * @param array<string, mixed> $value
     *
     * @return array<string, mixed>
     */
    private function getApplicableChannels(ChannelsAwareInterface $channelsAware, array $value): array
    {
        $channelCodes = $channelsAware
            ->getChannels()
            ->map(fn (BaseChannelInterface $channel) => (string) $channel->getCode())
            ->toArray()
        ;

        return array_unique(array_merge($channelCodes, array_keys($value)));
    }
}
