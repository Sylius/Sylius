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

namespace Sylius\Component\Resource\Translation;

use Sylius\Component\Resource\Model\TranslatableInterface;

interface TranslatableEntityLocaleAssignerInterface
{
    public function assignLocale(TranslatableInterface $translatableEntity): void;
}
