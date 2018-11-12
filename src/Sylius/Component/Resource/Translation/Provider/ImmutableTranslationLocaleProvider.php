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

namespace Sylius\Component\Resource\Translation\Provider;

final class ImmutableTranslationLocaleProvider implements TranslationLocaleProviderInterface
{
    /**
     * @var array
     */
    private $definedLocalesCodes;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    public function __construct(array $definedLocalesCodes, string $defaultLocaleCode)
    {
        $this->definedLocalesCodes = $definedLocalesCodes;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedLocalesCodes(): array
    {
        return $this->definedLocalesCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }
}
