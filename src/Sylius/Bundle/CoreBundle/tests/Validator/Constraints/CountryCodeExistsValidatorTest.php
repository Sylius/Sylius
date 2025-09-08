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
use Sylius\Bundle\CoreBundle\Validator\Constraints\CountryCodeExists;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CountryCodeExistsValidator;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CountryCodeExistsValidatorTest extends TestCase
{
    private MockObject&RepositoryInterface $countryRepository;

    private ExecutionContextInterface&MockObject $executionContext;

    private CountryCodeExistsValidator $validator;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(RepositoryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new CountryCodeExistsValidator($this->countryRepository);
        $this->validator->initialize($this->executionContext);
    }

    public function testItThrowsAnExceptionIfConstraintIsNotAnInstanceOfCountryCodeExists(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('country_code', $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfValueIsEmpty(): void
    {
        $this->countryRepository->expects($this->never())->method('findOneBy');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('', new CountryCodeExists());
    }

    public function testItDoesNothingIfCountryWithGivenCodeExists(): void
    {
        $country = $this->createMock(CountryInterface::class);

        $this->countryRepository->method('findOneBy')->with(['code' => 'country_code'])->willReturn($country);
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('country_code', new CountryCodeExists());
    }

    public function testItAddsAViolationIfCountryWithGivenCodeDoesNotExist(): void
    {
        $this->countryRepository
            ->method('findOneBy')
            ->with(['code' => 'country_code'])
            ->willReturn(null);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ code }}', 'country_code')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with('sylius.country.code.not_exist')
            ->willReturn($violationBuilder);

        $this->validator->validate('country_code', new CountryCodeExists());
    }
}
