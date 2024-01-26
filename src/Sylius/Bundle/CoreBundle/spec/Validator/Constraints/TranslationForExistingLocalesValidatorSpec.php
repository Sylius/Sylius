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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TranslationForExistingLocales;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class TranslationForExistingLocalesValidatorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($localeRepository);

        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_translatable(
        RepositoryInterface $localeRepository,
        ExecutionContextInterface $context,
        TranslatableInterface $value,
        TranslationInterface $translation,
    ): void {
        $localeRepository->findAll()->shouldNotBeCalled();

        $value->getTranslations()->shouldNotBeCalled();
        $translation->getLocale()->shouldNotBeCalled();

        $context->buildViolation((new TranslationForExistingLocales())->message)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new TranslationForExistingLocales()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_translation_for_existing_locales_constraint(
        RepositoryInterface $localeRepository,
        ExecutionContextInterface $context,
        Constraint $constraint,
        TranslatableInterface $value,
        TranslationInterface $translation,
    ): void {
        $localeRepository->findAll()->shouldNotBeCalled();

        $value->getTranslations()->shouldNotBeCalled();
        $translation->getLocale()->shouldNotBeCalled();

        $context->buildViolation((new TranslationForExistingLocales())->message)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_does_nothing_if_there_is_no_locales(
        RepositoryInterface $localeRepository,
        ExecutionContextInterface $context,
        TranslatableInterface $value,
        TranslationInterface $translation,
    ): void {
        $localeRepository->findAll()->willReturn([]);

        $value->getTranslations()->shouldNotBeCalled();
        $translation->getLocale()->shouldNotBeCalled();

        $context->buildViolation((new TranslationForExistingLocales())->message)->shouldNotBeCalled();

        $this->validate($value, new TranslationForExistingLocales());
    }

    function it_adds_a_violation_if_any_translations_locale_in_the_translatable_object_is_not_included_in_the_available_locales(
        RepositoryInterface $localeRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        TranslatableInterface $value,
        TranslationInterface $firstTranslation,
        TranslationInterface $secondTranslation,
        LocaleInterface $availableLocale,
    ): void {
        $availableLocale->getCode()->willReturn('en_US');
        $localeRepository->findAll()->willReturn([$availableLocale]);

        $value->getTranslations()->willReturn(new ArrayCollection([$firstTranslation, $secondTranslation]));
        $firstTranslation->getLocale()->willReturn('en_US');
        $secondTranslation->getLocale()->willReturn('NON_EXISTING_LOCALE');

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation((new TranslationForExistingLocales())->message)->willReturn($constraintViolationBuilder);

        $this->validate($value, new TranslationForExistingLocales());
    }

    function it_does_not_add_violation_if_the_translations_in_the_translatable_object_are_included_in_the_available_locales(
        RepositoryInterface $localeRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        TranslatableInterface $value,
        TranslationInterface $firstTranslation,
        TranslationInterface $secondTranslation,
        LocaleInterface $firstAvailableLocale,
        LocaleInterface $secondAvailableLocale,
    ): void {
        $firstAvailableLocale->getCode()->willReturn('en_US');
        $secondAvailableLocale->getCode()->willReturn('pl_PL');
        $localeRepository->findAll()->willReturn([$firstAvailableLocale, $secondAvailableLocale]);

        $value
            ->getTranslations()
            ->willReturn(new ArrayCollection([$firstTranslation->getWrappedObject(), $secondTranslation->getWrappedObject()]))
        ;
        $firstTranslation->getLocale()->willReturn('en_US');
        $secondTranslation->getLocale()->willReturn('pl_PL');

        $context->buildViolation((new TranslationForExistingLocales())->message)->shouldNotBeCalled();

        $this->validate($value, new TranslationForExistingLocales());
    }
}
