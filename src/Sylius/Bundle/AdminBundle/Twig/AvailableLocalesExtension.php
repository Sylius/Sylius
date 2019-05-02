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

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AvailableLocalesExtension extends AbstractExtension
{
    /** @var TranslationLocaleProviderInterface */
    private $localeProvider;

    public function __construct(TranslationLocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('sylius_available_locales',[$this, 'getDefinedLocaleCodes']),
        ];
    }

    public function getDefinedLocaleCodes(): array
    {
        return $this->localeProvider->getDefinedLocalesCodes();
    }
}
