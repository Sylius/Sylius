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

namespace Sylius\Component\Locale\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface LocaleInterface extends ResourceInterface, CodeAwareInterface, TimestampableInterface
{
    /**
     * @param string|null $locale
     *
     * @return string|null
     */
    public function getName(?string $locale = null): ?string;
}
