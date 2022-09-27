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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultLocalesStory extends Story implements DefaultLocalesStoryInterface
{
    public function __construct(private LocaleFactoryInterface $localeFactory, private string $baseLocaleCode)
    {
    }

    public function build(): void
    {
        foreach ($this->getDefaultLocaleCodes() as $localeCode) {
            $this->localeFactory::new()->withCode($localeCode)->create();
        }
    }

    public function getDefaultLocaleCodes(): array
    {
        $defaultLocaleCodes = [
            'en_US',
            'de_DE',
            'fr_FR',
            'pl_PL',
            'es_ES',
            'es_MX',
            'pt_PT',
            'zh_CN',
        ];

        if (!in_array($this->baseLocaleCode, $defaultLocaleCodes, true)) {
            $defaultLocaleCodes[] = $this->baseLocaleCode;
        }

        return $defaultLocaleCodes;
    }
}
