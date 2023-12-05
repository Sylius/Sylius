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

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class TranslationForExistingLocalesValidator extends ConstraintValidator
{
    public function __construct(private RepositoryInterface $localeRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, TranslatableInterface::class);
        Assert::isInstanceOf($constraint, TranslationForExistingLocales::class);

        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();

        if (empty($locales)) {
            return;
        }

        $localeCodes = array_map(fn (LocaleInterface $locale) => $locale->getCode(), $locales);

        $translations = $value->getTranslations();

        /** @var TranslationInterface $translation */
        foreach ($translations as $key => $translation) {
            if (!in_array($translation->getLocale(), $localeCodes, true)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%locales%', implode(', ', $localeCodes))
                    ->atPath(sprintf('translations[%s]', $key))
                    ->addViolation()
                ;
            }
        }
    }
}
