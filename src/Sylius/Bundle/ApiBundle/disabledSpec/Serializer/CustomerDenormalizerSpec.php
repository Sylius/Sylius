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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CustomerDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_customer_denormalizer_already_called';

    function let(ClockInterface $clock): void
    {
        $this->beConstructedWith($clock);
    }

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this->supportsDenormalization([], CustomerInterface::class, context: [self::ALREADY_CALLED => true])->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', CustomerInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_customer(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_does_nothing_if_user_verified_is_not_set(
        DenormalizerInterface $denormalizer,
        ClockInterface $clock,
        CustomerInterface $customer,
    ): void {
        $this->setDenormalizer($denormalizer);
        $denormalizer->denormalize([], CustomerInterface::class, null, [self::ALREADY_CALLED => true])->willReturn($customer);

        $this->denormalize([], CustomerInterface::class)->shouldReturn($customer);

        $clock->now()->shouldNotHaveBeenCalled();
    }

    public function it_changes_user_verified_from_false_to_null(
        DenormalizerInterface $denormalizer,
        ClockInterface $clock,
        CustomerInterface $customer,
    ): void {
        $this->setDenormalizer($denormalizer);
        $denormalizer->denormalize(['user' => ['verified' => null]], CustomerInterface::class, null, [self::ALREADY_CALLED => true])->willReturn($customer);

        $this->denormalize(['user' => ['verified' => false]], CustomerInterface::class)->shouldReturn($customer);

        $clock->now()->shouldNotHaveBeenCalled();
    }

    public function it_changes_user_verified_from_true_to_datetime(
        DenormalizerInterface $denormalizer,
        ClockInterface $clock,
        CustomerInterface $customer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $dateTime = new \DateTimeImmutable('2021-01-01T00:00:00+00:00');
        $clock->now()->willReturn($dateTime);
        $denormalizer->denormalize(['user' => ['verified' => '2021-01-01T00:00:00+00:00']], CustomerInterface::class, null, [self::ALREADY_CALLED => true])->willReturn($customer);

        $this->denormalize(['user' => ['verified' => true]], CustomerInterface::class)->shouldReturn($customer);
    }
}
