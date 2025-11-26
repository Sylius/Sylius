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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TranslationForExistingLocales;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TranslationForExistingLocalesValidator;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslationInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class TranslationForExistingLocalesValidatorTest extends TestCase
{
    private MockObject&RepositoryInterface $localeRepository;

    private ExecutionContextInterface&MockObject $context;

    private TranslationForExistingLocalesValidator $validator;

    protected function setUp(): void
    {
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new TranslationForExistingLocalesValidator($this->localeRepository);
        $this->validator->initialize($this->context);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testItThrowsExceptionIfValueIsNotTranslatable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->validator->validate(new \stdClass(), new TranslationForExistingLocales());
    }

    public function testItThrowsExceptionIfConstraintIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $value = $this->createMock(TranslatableInterface::class);
        $this->validator->validate($value, $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfThereAreNoLocales(): void
    {
        $this->localeRepository->method('findAll')->willReturn([]);

        $value = $this->createMock(TranslatableInterface::class);
        $value->expects($this->never())->method('getTranslations');

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, new TranslationForExistingLocales());
    }

    public function testItAddsViolationIfTranslationLocaleIsInvalid(): void
    {
        $locale = $this->createMock(LocaleInterface::class);
        $locale->method('getCode')->willReturn('en_US');

        $this->localeRepository->method('findAll')->willReturn([$locale]);

        $translation1 = $this->createMock(TranslationInterface::class);
        $translation2 = $this->createMock(TranslationInterface::class);

        $translation1->method('getLocale')->willReturn('en_US');
        $translation2->method('getLocale')->willReturn('fr_FR');

        $value = $this->createMock(TranslatableInterface::class);
        $value->method('getTranslations')->willReturn(new ArrayCollection([$translation1, $translation2]));

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects($this->once())
            ->method('setParameter')
            ->with('%locales%', 'en_US')
            ->willReturn($builder)
        ;

        $builder->expects($this->once())->method('atPath')->with('translations[1]')->willReturn($builder);
        $builder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with((new TranslationForExistingLocales())->message)
            ->willReturn($builder)
        ;

        $this->validator->validate($value, new TranslationForExistingLocales());
    }

    public function testItDoesNotAddViolationIfAllTranslationLocalesAreValid(): void
    {
        $locale1 = $this->createMock(LocaleInterface::class);
        $locale1->method('getCode')->willReturn('en_US');

        $locale2 = $this->createMock(LocaleInterface::class);
        $locale2->method('getCode')->willReturn('pl_PL');

        $this->localeRepository->method('findAll')->willReturn([$locale1, $locale2]);

        $translation1 = $this->createMock(TranslationInterface::class);
        $translation2 = $this->createMock(TranslationInterface::class);

        $translation1->method('getLocale')->willReturn('en_US');
        $translation2->method('getLocale')->willReturn('pl_PL');

        $value = $this->createMock(TranslatableInterface::class);
        $value->method('getTranslations')->willReturn(new ArrayCollection([$translation1, $translation2]));

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($value, new TranslationForExistingLocales());
    }
}
