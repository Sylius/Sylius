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
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneCannotContainItself;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneCannotContainItselfValidator;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ZoneCannotContainItselfValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private MockObject&ZoneInterface $zone;

    private MockObject&ZoneMemberInterface $zoneMember;

    private ZoneCannotContainItselfValidator $zoneCannotContainItselfValidator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->zone = $this->createMock(ZoneInterface::class);
        $this->zoneMember = $this->createMock(ZoneMemberInterface::class);

        $this->zoneCannotContainItselfValidator = new ZoneCannotContainItselfValidator();
        $this->zoneCannotContainItselfValidator->initialize($this->executionContext);
    }

    public function testImplementsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidatorInterface::class, $this->zoneCannotContainItselfValidator);
    }

    public function testDoesNothingIfValueIsNull(): void
    {
        $this->executionContext->expects($this->never())->method('addViolation')->with($this->any());

        $this->zoneCannotContainItselfValidator->validate(null, new ZoneCannotContainItself());
    }

    public function testThrowsAnExceptionIfConstraintIsNotOfExpectedType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        $this->zoneCannotContainItselfValidator->validate('', $constraint);
    }

    public function testDoesNotAddViolationIfZoneDoesNotContainItselfInMembers(): void
    {
        $this->zone->expects($this->once())->method('getCode')->willReturn('WORLD');
        $this->zone->expects($this->once())->method('getType')->willReturn(ZoneInterface::TYPE_ZONE);
        $this->zoneMember->expects($this->once())->method('getCode')->willReturn('EU');
        $this->zoneMember->expects($this->once())->method('getBelongsTo')->willReturn($this->zone);
        $this->executionContext->expects($this->never())->method('addViolation')->with($this->any());

        $this->zoneCannotContainItselfValidator->validate([$this->zoneMember], new ZoneCannotContainItself());
    }

    public function testDoesNotAddViolationForZonesOfCountryTypeContainingAMemberWithSameCode(): void
    {
        $this->zone->expects($this->never())->method('getCode')->willReturn('US');
        $this->zone->expects($this->once())->method('getType')->willReturn(ZoneInterface::TYPE_COUNTRY);
        $this->zoneMember->expects($this->never())->method('getCode')->willReturn('US');
        $this->zoneMember->expects($this->once())->method('getBelongsTo')->willReturn($this->zone);
        $this->executionContext->expects($this->never())->method('addViolation')->with($this->any());

        $this->zoneCannotContainItselfValidator->validate([$this->zoneMember], new ZoneCannotContainItself());
    }

    public function testDoesNotAddViolationForZonesOfProvinceTypeContainingAMemberWithSameCode(): void
    {
        $this->zone->expects($this->never())->method('getCode')->willReturn('RO-B');
        $this->zone->expects($this->once())->method('getType')->willReturn(ZoneInterface::TYPE_PROVINCE);
        $this->zoneMember->expects($this->never())->method('getCode')->willReturn('RO-B');
        $this->zoneMember->expects($this->once())->method('getBelongsTo')->willReturn($this->zone);
        $this->executionContext->expects($this->never())->method('addViolation')->with($this->any());

        $this->zoneCannotContainItselfValidator->validate([$this->zoneMember], new ZoneCannotContainItself());
    }

    public function testAddsViolationIfZoneContainsItselfInMembers(): void
    {
        $this->zone->expects($this->once())->method('getCode')->willReturn('EU');
        $this->zone->expects($this->once())->method('getType')->willReturn(ZoneInterface::TYPE_ZONE);
        $this->zoneMember->expects($this->once())->method('getCode')->willReturn('EU');
        $this->zoneMember->expects($this->once())->method('getBelongsTo')->willReturn($this->zone);
        $this->executionContext->expects($this->once())->method('addViolation');

        $this->zoneCannotContainItselfValidator->validate([$this->zoneMember], new ZoneCannotContainItself());
    }
}
