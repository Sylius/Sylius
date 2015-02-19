<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

/**
 * TranslatableListener interface.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
interface TranslatableListenerInterface
{
    /**
     * Set the current locale
     *
     * @param  string $currentLocale
     * @return self
     */
    public function setCurrentLocale($currentLocale);
}
