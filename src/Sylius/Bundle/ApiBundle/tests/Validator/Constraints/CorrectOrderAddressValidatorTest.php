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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddress;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddressValidator;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CorrectOrderAddressValidatorTest extends TestCase
{
    private MockObject&RepositoryInterface $countryRepository;

    private CorrectOrderAddressValidator $correctOrderAddressValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->countryRepository = $this->createMock(RepositoryInterface::class);
        $this->correctOrderAddressValidator = new CorrectOrderAddressValidator($this->countryRepository);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->correctOrderAddressValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfAddressOrderCommand(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->correctOrderAddressValidator->validate(new CompleteOrder('TOKEN'), new CorrectOrderAddress());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfAddingEligibleProductVariantToCart(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $invalidConstraint = $this->createMock(Constraint::class);

        $command = new UpdateCart(
            orderTokenValue: 'token',
            shippingAddress: null,
            billingAddress: null,
        );

        $this->correctOrderAddressValidator->validate($command, $invalidConstraint);
    }

    public function testAddsViolationIfBillingAddressHasIncorrectCountryCode(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $this->correctOrderAddressValidator->initialize($executionContextMock);
        $billingAddressMock->expects(self::once())->method('getCountryCode')->willReturn('united_russia');
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.country.not_exist', ['%countryCode%' => 'united_russia'])
        ;
        $this->correctOrderAddressValidator->validate(
            new UpdateCart(
                orderTokenValue: 'TOKEN',
                email: 'john@doe.com',
                billingAddress: $billingAddressMock,
            ),
            new CorrectOrderAddress(),
        );
    }

    public function testAddsViolationIfBillingAddressHasNotCountryCode(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $this->correctOrderAddressValidator->initialize($executionContextMock);
        $billingAddressMock->expects(self::once())->method('getCountryCode')->willReturn(null);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.address.without_country')
        ;
        $this->correctOrderAddressValidator->validate(
            new UpdateCart(
                orderTokenValue: 'TOKEN',
                email: 'john@doe.com',
                billingAddress: $billingAddressMock,
            ),
            new CorrectOrderAddress(),
        );
    }

    public function testAddsViolationIfShippingAddressHasIncorrectCountryCode(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        /** @var CountryInterface|MockObject $usaMock */
        $usaMock = $this->createMock(CountryInterface::class);

        $this->correctOrderAddressValidator->initialize($executionContextMock);

        $billingAddressMock->expects(self::once())
            ->method('getCountryCode')
            ->willReturn('US');

        $shippingAddressMock->expects(self::once())
            ->method('getCountryCode')
            ->willReturn('united_russia');

        $this->countryRepository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnMap([
                [['code' => 'US'], $usaMock],
                [['code' => 'united_russia'], null],
            ]);

        $executionContextMock->expects(self::once())
            ->method('addViolation')
            ->with('sylius.country.not_exist', ['%countryCode%' => 'united_russia']);

        $this->correctOrderAddressValidator->validate(
            new UpdateCart(
                orderTokenValue: 'TOKEN',
                email: 'john@doe.com',
                billingAddress: $billingAddressMock,
                shippingAddress: $shippingAddressMock,
            ),
            new CorrectOrderAddress(),
        );
    }

    public function testAddsViolationIfShippingAddressAndBillingAddressHaveIncorrectCountryCode(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);

        $this->correctOrderAddressValidator->initialize($executionContextMock);

        $billingAddressMock->expects(self::once())
            ->method('getCountryCode')
            ->willReturn('euroland');

        $shippingAddressMock->expects(self::once())
            ->method('getCountryCode')
            ->willReturn('united_russia');

        $this->countryRepository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnMap([
                [['code' => 'euroland'], null],
                [['code' => 'united_russia'], null],
            ]);

        $violations = [];
        $executionContextMock
            ->expects($this->exactly(2))
            ->method('addViolation')
            ->willReturnCallback(function ($message, $params) use (&$violations) {
                $violations[] = ['message' => $message, 'params' => $params];
            });

        $this->correctOrderAddressValidator->validate(
            new UpdateCart(
                orderTokenValue: 'TOKEN',
                email: 'john@doe.com',
                billingAddress: $billingAddressMock,
                shippingAddress: $shippingAddressMock,
            ),
            new CorrectOrderAddress(),
        );

        $this->assertEquals([
            [
                'message' => 'sylius.country.not_exist',
                'params' => ['%countryCode%' => 'euroland'],
            ],
            [
                'message' => 'sylius.country.not_exist',
                'params' => ['%countryCode%' => 'united_russia'],
            ],
        ], $violations);
    }
}
