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

use Symfony\Component\BrowserKit\AbstractBrowser;

final class ApiPlatformStateMachineClient implements ApiPlatformStateMachineClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    public function __construct(AbstractBrowser $client)
    {
        $this->client = $client;
    }

    public function applyTransition(string $resource, string $id, string $transition): void
    {
        $this->client->request(
            'PATCH',
            sprintf('/new-api/%s/%s/apply_transition/%s', $resource, $id, $transition),
            [], [],
            ['HTTP_ACCEPT' => 'application/ld+json']
        );
    }
}
