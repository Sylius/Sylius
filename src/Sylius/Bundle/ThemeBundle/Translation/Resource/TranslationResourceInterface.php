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

namespace Sylius\Bundle\ThemeBundle\Translation\Resource;

interface TranslationResourceInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getLocale(): string;

    /**
     * @return string
     */
    public function getFormat(): string;

    /**
     * @return string
     */
    public function getDomain(): string;
}
