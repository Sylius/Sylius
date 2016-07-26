<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Context;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleContextInterface
{
    /**
     * @return string
     *
     * @throws LocaleNotFoundException
     */
    public function getLocaleCode();
}
