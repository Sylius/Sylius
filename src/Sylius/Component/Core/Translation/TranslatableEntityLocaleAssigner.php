<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Translation;

use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

final class TranslatableEntityLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        private TranslationLocaleProviderInterface $translationLocaleProvider,
        private ?CLIContextCheckerInterface $commandBasedChecker = null
    ) {
        if ($this->commandBasedChecker === null) {
            @trigger_error(
                'Not passing CommandBasedContextCheckedInterface explicitly as the third argument is deprecated since 1.11 and will be prohibited in 2.0.',
                \E_USER_DEPRECATED
            );
        }
    }

    public function assignLocale(TranslatableInterface $translatableEntity): void
    {
        $fallbackLocale = $this->translationLocaleProvider->getDefaultLocaleCode();
        $translatableEntity->setFallbackLocale($fallbackLocale);

        if ($this->commandBasedChecker !== null && $this->commandBasedChecker->isExecutedFromCLI()) {
            $translatableEntity->setCurrentLocale($fallbackLocale);

            return;
        }

        try {
            $currentLocale = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            $currentLocale = $fallbackLocale;
        }

        $translatableEntity->setCurrentLocale($currentLocale);
    }
}
