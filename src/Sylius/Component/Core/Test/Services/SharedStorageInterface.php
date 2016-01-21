<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SharedStorageInterface
{
    /**
     * @param string $key
     *
     * @return ResourceInterface
     */
    public function getCurrentResource($key);

    /**
     * @param string $key
     * @param ResourceInterface $resource
     * @param bool $override
     *
     * @throws \RuntimeException
     */
    public function setCurrentResource($key, ResourceInterface $resource, $override = false);

    /**
     * @param array $clipboard
     * @param bool $override
     *
     * @throws \RuntimeException
     */
    public function setClipboard(array $clipboard, $override = false);
}
