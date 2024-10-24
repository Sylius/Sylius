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

interface ResponseLoaderInterface
{
    /**
     * @param string $source
     *
     * @return array
     */
    public function getMockedResponse($source);

    /**
     * @param string $source
     *
     * @return array
     */
    public function getExpectedResponse($source);
}
