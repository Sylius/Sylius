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

namespace Tests\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneMemberGroup;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneMemberGroupValidator;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ZoneMemberGroupValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private ZoneMemberGroupValidator $zoneMemberGroupValidator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->zoneMemberGroupValidator = new ZoneMemberGroupValidator(['zone_two' => ['Default', 'zone_two']]);
        $this->zoneMemberGroupValidator->initialize($this->context);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfZoneMemberGroup(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);
        /** @var ZoneMemberInterface&MockObject $zoneMember */
        $zoneMember = $this->createMock(ZoneMemberInterface::class);
        $this->expectException(UnexpectedTypeException::class);

        $this->zoneMemberGroupValidator->validate($zoneMember, $constraint);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfZoneMember(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->zoneMemberGroupValidator->validate(new \stdClass(), new ZoneMemberGroup());
    }

    public function testCallsAValidatorWithGroup(): void
    {
        /** @var ZoneMemberInterface&MockObject $zoneMember */
        $zoneMember = $this->createMock(ZoneMemberInterface::class);
        /** @var ZoneInterface&MockObject $zone */
        $zone = $this->createMock(ZoneInterface::class);
        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        /** @var ContextualValidatorInterface&MockObject $contextualValidator */
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);

        $zoneMember->expects($this->once())->method('getBelongsTo')->willReturn($zone);
        $zone->expects($this->once())->method('getType')->willReturn('zone_two');
        $this->context->expects($this->once())->method('getValidator')->willReturn($validator);
        $validator->expects($this->once())->method('inContext')->with($this->context)->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate')->with($zoneMember, null, ['Default', 'zone_two'])->willReturn($contextualValidator);

        $this->zoneMemberGroupValidator->validate($zoneMember, new ZoneMemberGroup(['groups' => ['Default', 'test_group']]));
    }

    public function testCallsValidatorWithDefaultGroupsIfNoneProvidedForZoneMemberType(): void
    {
        /** @var ZoneMemberInterface&MockObject $zoneMember */
        $zoneMember = $this->createMock(ZoneMemberInterface::class);
        /** @var ZoneInterface&MockObject $zone */
        $zone = $this->createMock(ZoneInterface::class);
        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        /** @var ContextualValidatorInterface&MockObject $contextualValidator */
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);

        $zoneMember->expects($this->once())->method('getBelongsTo')->willReturn($zone);
        $zone->expects($this->once())->method('getType')->willReturn('zone_one');
        $this->context->expects($this->once())->method('getValidator')->willReturn($validator);
        $validator->expects($this->once())->method('inContext')->with($this->context)->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate')->with($zoneMember, null, ['Default', 'test_group'])->willReturn($contextualValidator);

        $this->zoneMemberGroupValidator->validate($zoneMember, new ZoneMemberGroup(['groups' => ['Default', 'test_group']]));
    }

    public function testCallsValidatorWithDefaultGroupsIfZoneIsNull(): void
    {
        /** @var ZoneMemberInterface&MockObject $zoneMember */
        $zoneMember = $this->createMock(ZoneMemberInterface::class);
        /** @var ValidatorInterface&MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        /** @var ContextualValidatorInterface&MockObject $contextualValidator */
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);

        $zoneMember->expects($this->once())->method('getBelongsTo')->willReturn(null);
        $this->context->expects($this->once())->method('getValidator')->willReturn($validator);
        $validator->expects($this->once())->method('inContext')->with($this->context)->willReturn($contextualValidator);
        $contextualValidator->expects($this->once())->method('validate')->with($zoneMember, null, ['Default', 'test_group'])->willReturn($contextualValidator);

        $this->zoneMemberGroupValidator->validate($zoneMember, new ZoneMemberGroup(['groups' => ['Default', 'test_group']]));
    }
}
