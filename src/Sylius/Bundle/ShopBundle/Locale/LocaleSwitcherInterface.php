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

namespace Sylius\Bundle\ShopBundle\Locale;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface LocaleSwitcherInterface
{
    /**
     * @param Request $request
     * @param string $localeCode
     *
     * @return Response
     */
    public function handle(Request $request, $localeCode);
}
