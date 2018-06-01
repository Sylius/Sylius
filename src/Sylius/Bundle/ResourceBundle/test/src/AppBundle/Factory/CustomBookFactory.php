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

namespace AppBundle\Factory;

use AppBundle\Entity\Book;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class CustomBookFactory
{
    /** @var string */
    private $className;

    /** @var TranslationLocaleProviderInterface */
    private $localeProvider;

    public function __construct(string $className, TranslationLocaleProviderInterface $localeProvider)
    {
        $this->className = $className;
        $this->localeProvider = $localeProvider;
    }

    public function createCustom(): Book
    {
        /** @var Book $book */
        $book = new $this->className;

        $book->setCurrentLocale($this->localeProvider->getDefaultLocaleCode());
        $book->setFallbackLocale($this->localeProvider->getDefaultLocaleCode());

        return $book;
    }
}
