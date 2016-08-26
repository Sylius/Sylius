<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleHelperInterface extends HelperInterface
{
    /**
     * @param string $localeCode
     *
     * @return string
     */
    public function convertCodeToName($localeCode);
}
