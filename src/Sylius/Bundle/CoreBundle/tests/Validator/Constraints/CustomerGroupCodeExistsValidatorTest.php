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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CustomerGroupCodeExists;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CustomerGroupCodeExistsValidator;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Repository\CustomerGroupRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CustomerGroupCodeExistsValidatorTest extends TestCase
{
    private CustomerGroupRepositoryInterface&MockObject $customerGroupRepository;

    private ExecutionContextInterface&MockObject $executionContext;

    private CustomerGroupCodeExistsValidator $validator;

    protected function setUp(): void
    {
        $this->customerGroupRepository = $this->createMock(CustomerGroupRepositoryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new CustomerGroupCodeExistsValidator($this->customerGroupRepository);
        $this->validator->initialize($this->executionContext);
    }

    public function testItThrowsAnExceptionIfConstraintIsNotAnInstanceOfCustomerGroupCodeExists(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('customer_group_code', $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfValueIsEmpty(): void
    {
        $this->customerGroupRepository->expects($this->never())->method('findOneBy');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('', new CustomerGroupCodeExists());
    }

    public function testItDoesNothingIfCustomerGroupWithGivenCodeExists(): void
    {
        $customerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->customerGroupRepository
            ->method('findOneBy')
            ->with(['code' => 'customer_group_code'])
            ->willReturn($customerGroup)
        ;

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('customer_group_code', new CustomerGroupCodeExists());
    }

    public function testItAddsViolationIfCustomerGroupWithGivenCodeDoesNotExist(): void
    {
        $this->customerGroupRepository
            ->method('findOneBy')
            ->with(['code' => 'customer_group_code'])
            ->willReturn(null)
        ;

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ code }}', 'customer_group_code')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with('sylius.customer_group.code.not_exist')
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate('customer_group_code', new CustomerGroupCodeExists());
    }
}
