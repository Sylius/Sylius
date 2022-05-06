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

namespace Sylius\Behat\Client;

interface RequestBuilderFactoryInterface
{
    public function get(string ...$resources): RequestBuilder;

    public function post(string ...$resources): RequestBuilder;

    public function put(string ...$resources): RequestBuilder;

    public function patch(string ...$resources): RequestBuilder;

    public function delete(string ...$resources): RequestBuilder;
}
