<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Development;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface FakeHostnameProviderInterface
{
    /**
     * @param Request $request
     *
     * @return string
     */
    public function getHostname(Request $request);
}
