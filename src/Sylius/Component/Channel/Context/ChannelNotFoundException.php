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
    private static const EXCEPTION_MESSAGE = 'Channel could not be found! Tip: You can use the Web Debug Toolbar to switch between channels in development.';

    /**
     * {@inheritdoc}
     */
    public function __construct($messageOrPreviousException = null, ?\Throwable $previousException = null)
    {
        if ($messageOrPreviousException instanceof \Throwable) {
            parent::__construct(self::EXCEPTION_MESSAGE, 0, $messageOrPreviousException);
        }

        else if (is_string($messageOrPreviousException)) {
            parent::__construct($messageOrPreviousException, 0, $previousException);
        }

        else {
            parent::__construct(self::EXCEPTION_MESSAGE, 0, $previousException);
        }
    }
}
