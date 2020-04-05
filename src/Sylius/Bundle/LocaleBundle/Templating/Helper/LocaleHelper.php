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

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

final class LocaleHelper extends Helper implements LocaleHelperInterface
{
    /** @var LocaleConverterInterface */
    private $localeConverter;

    /** @var LocaleContextInterface|null */
    private $localeContext;

    public function __construct(LocaleConverterInterface $localeConverter, ?LocaleContextInterface $localeContext = null)
    {
        if (null === $localeContext) {
            @trigger_error('Not passing LocaleContextInterface explicitly as the second argument is deprecated since 1.4 and will be prohibited in 2.0', \E_USER_DEPRECATED);
        }

        $this->localeConverter = $localeConverter;
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function convertCodeToName(string $code, ?string $localeCode = null): ?string
    {
        return $this->localeConverter->convertCodeToName($code, $this->getLocaleCode($localeCode));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_locale';
    }

    private function getLocaleCode(?string $localeCode): ?string
    {
        if (null !== $localeCode) {
            return $localeCode;
        }

        if (null === $this->localeContext) {
            return null;
        }

        try {
            return $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $exception) {
            return null;
        }
    }
}
