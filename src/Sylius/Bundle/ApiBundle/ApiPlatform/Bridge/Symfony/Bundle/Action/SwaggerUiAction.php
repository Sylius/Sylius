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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Bundle\Action;

use ApiPlatform\Core\Api\FormatsProviderInterface;
use ApiPlatform\Core\Documentation\Documentation;
use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Environment as TwigEnvironment;

/**
 * Displays the documentation.
 *
 * @internal
 *
 * This class will be probably removed in Sylius 1.9
 */
final class SwaggerUiAction
{
    private ?array $formats;

    private $formatsProvider;

    /**
     * @param int[] $swaggerVersions
     */
    public function __construct(
        private ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory,
        private NormalizerInterface $normalizer,
        private TwigEnvironment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private string $title = '',
        private string $description = '',
        private string $version = '',
        $formats = [],
        private $oauthEnabled = false,
        private $oauthClientId = '',
        private $oauthClientSecret = '',
        private $oauthType = '',
        private $oauthFlow = '',
        private $oauthTokenUrl = '',
        private $oauthAuthorizationUrl = '',
        private $oauthScopes = [],
        private bool $showWebby = true,
        private bool $swaggerUiEnabled = false,
        private bool $reDocEnabled = false,
        private bool $graphqlEnabled = false,
        private bool $graphiQlEnabled = false,
        private bool $graphQlPlaygroundEnabled = false,
        private array $swaggerVersions = [2, 3],
    ) {
        if (\is_array($formats)) {
            $this->formats = $formats;

            return;
        }

        @trigger_error(sprintf(
            'Passing an array or an instance of "%s" as 5th parameter of the constructor of "%s" is deprecated since API Platform 2.5, pass an array instead',
            FormatsProviderInterface::class,
            __CLASS__,
        ), \E_USER_DEPRECATED);
        $this->formatsProvider = $formats;
    }

    public function __invoke(Request $request)
    {
        $attributes = RequestAttributesExtractor::extractAttributes($request);

        // BC check to be removed in 3.0
        if (null === $this->formatsProvider) {
            $formats = $attributes ? $this
                ->resourceMetadataFactory
                ->create($attributes['resource_class'])
                ->getOperationAttribute($attributes, 'output_formats', [], true) : $this->formats;
        } else {
            $formats = $this->formatsProvider->getFormatsFromAttributes($attributes);
        }

        $documentation = new Documentation($this->resourceNameCollectionFactory->create(), $this->title, $this->description, $this->version);

        return new Response($this->twig->render('@SyliusApi/SwaggerUi/index.html.twig', $this->getContext($request, $documentation) + ['formats' => $formats]));
    }

    /**
     * Gets the base Twig context.
     */
    private function getContext(Request $request, Documentation $documentation): array
    {
        $context = [
            'title' => $this->title,
            'description' => $this->description,
            'showWebby' => $this->showWebby,
            'swaggerUiEnabled' => $this->swaggerUiEnabled,
            'reDocEnabled' => $this->reDocEnabled,
            'graphqlEnabled' => $this->graphqlEnabled,
            'graphiQlEnabled' => $this->graphiQlEnabled,
            'graphQlPlaygroundEnabled' => $this->graphQlPlaygroundEnabled,
        ];

        $swaggerContext = ['spec_version' => $request->query->getInt('spec_version', $this->swaggerVersions[0] ?? 2)];
        if ('' !== $baseUrl = $request->getBaseUrl()) {
            $swaggerContext['base_url'] = $baseUrl;
        }

        $swaggerData = [
            'url' => $this->urlGenerator->generate('api_doc', ['format' => 'json']),
            'spec' => $this->normalizer->normalize($documentation, 'json', $swaggerContext),
        ];

        $swaggerData['oauth'] = [
            'enabled' => $this->oauthEnabled,
            'clientId' => $this->oauthClientId,
            'clientSecret' => $this->oauthClientSecret,
            'type' => $this->oauthType,
            'flow' => $this->oauthFlow,
            'tokenUrl' => $this->oauthTokenUrl,
            'authorizationUrl' => $this->oauthAuthorizationUrl,
            'scopes' => $this->oauthScopes,
        ];

        if ($request->isMethodSafe() && null !== $resourceClass = $request->attributes->get('_api_resource_class')) {
            $swaggerData['id'] = $request->attributes->get('id');
            $swaggerData['queryParameters'] = $request->query->all();

            $metadata = $this->resourceMetadataFactory->create($resourceClass);
            $swaggerData['shortName'] = $metadata->getShortName();

            if (null !== $collectionOperationName = $request->attributes->get('_api_collection_operation_name')) {
                $swaggerData['operationId'] = sprintf('%s%sCollection', $collectionOperationName, ucfirst($swaggerData['shortName']));
            } elseif (null !== $itemOperationName = $request->attributes->get('_api_item_operation_name')) {
                $swaggerData['operationId'] = sprintf('%s%sItem', $itemOperationName, ucfirst($swaggerData['shortName']));
            } elseif (null !== $subresourceOperationContext = $request->attributes->get('_api_subresource_context')) {
                $swaggerData['operationId'] = $subresourceOperationContext['operationId'];
            }

            [$swaggerData['path'], $swaggerData['method']] = $this->getPathAndMethod($swaggerData);
        }

        return $context + ['swagger_data' => $swaggerData];
    }

    private function getPathAndMethod(array $swaggerData): array
    {
        foreach ($swaggerData['spec']['paths'] as $path => $operations) {
            foreach ($operations as $method => $operation) {
                if ($operation['operationId'] === $swaggerData['operationId']) {
                    return [$path, $method];
                }
            }
        }

        throw new RuntimeException(sprintf('The operation "%s" cannot be found in the Swagger specification.', $swaggerData['operationId']));
    }
}
