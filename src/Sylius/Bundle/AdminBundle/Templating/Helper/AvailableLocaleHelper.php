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

namespace Sylius\Bundle\AdminBundle\Templating\Helper;

use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

class AvailableLocaleHelper extends Helper
{
    /** @var TranslationLocaleProviderInterface */
    private $localeProvider;

    public function __construct(TranslationLocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    public function getDefinedLocaleCodes(): array
    {
        return $this->localeProvider->getDefinedLocalesCodes();
    }

    public function getName(): string
    {
        return 'sylius_available_locales_helper';
    }
}
