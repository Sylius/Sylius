<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Locale\Model;

use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TimestampableInterface;
use Sylius\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleInterface extends ResourceInterface, CodeAwareInterface, TimestampableInterface, ToggleableInterface
{
    /**
     * @param string|null $locale
     *
     * @return string
     */
    public function getName($locale = null);
}
