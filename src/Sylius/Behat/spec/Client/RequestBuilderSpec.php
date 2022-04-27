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

namespace spec\Sylius\Behat\Client;

use PhpSpec\ObjectBehavior;
use Sylius\Behat\Client\RequestBuilder;
use Sylius\Behat\Client\RequestInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class RequestBuilderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('create', ['URI', 'METHOD']);
    }

    function it_builds_request(): void
    {
        $this->build()->shouldReturnAnInstanceOf(RequestInterface::class);
    }

    function it_builds_request_with_parameter(): void
    {
        $this->withParameter('key', 'value')->shouldReturnAnInstanceOf(RequestBuilder::class);
        $this->build()->parameters()->shouldReturn(['key' => 'value']);
    }

    function it_builds_request_with_header(): void
    {
        $this->withHeader('key', 'value')->shouldReturnAnInstanceOf(RequestBuilder::class);
        $this->build()->headers()->shouldReturn(['key' => 'value']);
    }

    function it_builds_request_with_file(): void
    {
        $file = new UploadedFile(__DIR__ . '/ford.jpg', 'ford.jpg', null, null, true);
        $this->withFile('key', $file)->shouldReturnAnInstanceOf(RequestBuilder::class);
        $this->build()->files()->shouldReturn(['key' => $file]);
    }
}
