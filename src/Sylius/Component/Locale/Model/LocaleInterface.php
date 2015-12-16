<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleInterface extends TimestampableInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @param string|null $locale
     *
     * @return string
     */
    public function getName($locale = null);
}
