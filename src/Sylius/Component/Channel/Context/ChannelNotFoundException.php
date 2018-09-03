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
    public function __construct($messageOrPreviousException = null, ?\Throwable $previousException = null)
    {
        $message = 'Channel could not be found! Tip: You can use the Web Debug Toolbar to switch between channels in development.';

        if ($messageOrPreviousException instanceof \Throwable) {
            @trigger_error('Passing previous exception as the first argument is deprecated since 1.2 and will be prohibited since 2.0.', E_USER_DEPRECATED);
            $previousException = $messageOrPreviousException;
        }

        if (is_string($messageOrPreviousException)) {
            $message = $messageOrPreviousException;
        }

        parent::__construct($message, 0, $previousException);
    }
}
