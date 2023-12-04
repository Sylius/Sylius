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

namespace Sylius\Behat\Service;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;

class SharedStorageChannelContext implements ChannelContextInterface
{
    public function __construct(private SharedStorageInterface $sharedStorage)
    {
    }

    public function getChannel(): ChannelInterface
    {
        if (!$this->sharedStorage->has('channel')) {
            throw new ChannelNotFoundException();
        }

        return $this->sharedStorage->get('channel');
    }
}
