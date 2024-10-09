<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Wrapper;

use Symfony\Component\HttpFoundation\Request;

interface SymfonyRequestWrapperInterface
{
    /**
     * @param Request $request
     * @return array{
     *      'http_request'?: array{
     *          'uri'?: string,
     *          'method'?: string,
     *          'query'?: array<string, array<int, bool|float|int|string>|bool|float|int|string>,
     *          'request'?: array<string, array<int, bool|float|int|string>|bool|float|int|string>,
     *          'headers'?: array<string, array<int, string|null>>,
     *          'content'?: string,
     *          'client_ip'?: string,
     *      },
     *  }
     */
    public function wrap(Request $request): array;
}
