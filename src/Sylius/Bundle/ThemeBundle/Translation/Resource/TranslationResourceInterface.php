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

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface TranslationResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @return string
     */
    public function getDomain();
}
