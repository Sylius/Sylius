<?php

declare(strict_types=1);

namespace Sylius\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;

abstract class SessionAwareAjaxTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $requestStack = self::$kernel->getContainer()->get('request_stack');
        try {
            $requestStack->getSession();
        } catch (SessionNotFoundException) {
            $session = self::$kernel->getContainer()->get('session_factory.public')->createSession();
            $request = new Request();
            $request->setSession($session);
            $requestStack->push($request);
        }
    }
}
