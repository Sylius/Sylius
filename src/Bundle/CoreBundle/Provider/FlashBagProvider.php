<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Provider;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class FlashBagProvider
{
    public static function getFlashBag(
        FlashBagInterface|RequestStack|SessionInterface $requestStackSessionOrFlashBag,
    ): FlashBagInterface {
        if ($requestStackSessionOrFlashBag instanceof FlashBagInterface) {
            return $requestStackSessionOrFlashBag;
        }

        if ($requestStackSessionOrFlashBag instanceof SessionInterface) {
            $flashBag = $requestStackSessionOrFlashBag->getBag('flashes');
            Assert::isInstanceOf($flashBag, FlashBagInterface::class);

            return $flashBag;
        }

        $flashBag = $requestStackSessionOrFlashBag->getSession()->getBag('flashes');
        Assert::isInstanceOf($flashBag, FlashBagInterface::class);

        return $flashBag;
    }
}
