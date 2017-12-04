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

namespace Sylius\Component\Channel\Context;

class ChannelNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(?\Exception $previousException = null)
    {
        parent::__construct('Channel could not be found! Tip: You can use the Web Debug Toolbar to switch between channels in development.', 0, $previousException);
    }
}
