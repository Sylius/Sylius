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

namespace Sylius\Bundle\CoreBundle\Provider;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class FlashBagProvider
{
    public static function getFlashBag(
        RequestStack|SessionInterface|FlashBagInterface $requestStackSessionOrFlashBag,
    ): FlashBagInterface {
        if ($requestStackSessionOrFlashBag instanceof FlashBagInterface) {
            return $requestStackSessionOrFlashBag;
        }

        if ($requestStackSessionOrFlashBag instanceof SessionInterface) {
            return $requestStackSessionOrFlashBag->getBag('flashes');
        }

        return $requestStackSessionOrFlashBag->getSession()->getBag('flashes');
    }
}
