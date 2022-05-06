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

use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class Helper
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RequestBuilderFactoryInterface $requestBuilderFactory,
        private ApiClientInterface $apiClient,
        private string $authorizationHeader,
    ) {
    }

    public function update(callable $closure): void
    {
        /** @var RequestBuilder $requestBuilder */
        $requestBuilder = $this->sharedStorage->get('request_builder');

        $requestBuilder = $closure($requestBuilder);

        $this->sharedStorage->set('request_builder', $requestBuilder);
    }

    public function authorize(RequestBuilder $requestBuilder): RequestBuilder
    {
        return $requestBuilder->withHeader(
            sprintf('HTTP_%s', $this->authorizationHeader),
            sprintf('Bearer %s',$this->sharedStorage->get('token'))
        );
    }

    public function preparePut(string $section, string $resource, string $id): RequestBuilder
    {
        $showRequest = $this->requestBuilderFactory->get($section, $resource, $id);
        $showRequest = $this->authorize($showRequest);
        $showResponse = $this->apiClient->executeCustomRequest($showRequest->build());

        $updateRequestBuilder = $this->requestBuilderFactory->put($section, $resource, $id);
        $updateRequestBuilder = $this->authorize($updateRequestBuilder);

        Assert::notFalse($showResponse->getContent());
        $updateRequestBuilder->withContent(json_decode($showResponse->getContent(), true));

        return $updateRequestBuilder;
    }
}
