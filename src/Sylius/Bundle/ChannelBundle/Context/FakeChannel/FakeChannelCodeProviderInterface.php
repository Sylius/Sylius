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

namespace Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface FakeChannelCodeProviderInterface
{
    /**
     * @param Request $request
     *
     * @return string|null
     */
    public function getCode(Request $request);
}
