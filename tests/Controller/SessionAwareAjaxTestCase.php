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

namespace Sylius\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;

abstract class SessionAwareAjaxTestCase extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $requestStack = self::getContainer()->get('request_stack');

        try {
            $requestStack->getSession();
        } catch (SessionNotFoundException) {
            $session = self::getContainer()->get('session.factory')->createSession();
            $request = new Request();
            $request->setSession($session);
            $requestStack->push($request);
        }
    }
}
