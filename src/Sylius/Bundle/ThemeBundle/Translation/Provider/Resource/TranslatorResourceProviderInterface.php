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

namespace Sylius\Bundle\ThemeBundle\Translation\Provider\Resource;

use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface TranslatorResourceProviderInterface
{
    /**
     * @return TranslationResourceInterface[]
     */
    public function getResources();

    /**
     * @return array
     */
    public function getResourcesLocales();
}
