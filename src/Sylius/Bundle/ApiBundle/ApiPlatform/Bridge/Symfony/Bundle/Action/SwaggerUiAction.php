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
    private ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory;

    private ResourceMetadataFactoryInterface $resourceMetadataFactory;

    private NormalizerInterface $normalizer;

    private TwigEnvironment $twig;

    private UrlGeneratorInterface $urlGenerator;

    private string $title;

    private string $description;

    private string $version;

    private bool $showWebby;

    private ?array $formats;

    private $oauthEnabled;

    private $oauthClientId;

    private $oauthClientSecret;

    private $oauthType;

    private $oauthFlow;

    private $oauthTokenUrl;

    private $oauthAuthorizationUrl;

    private $oauthScopes;

    private $formatsProvider;

    private bool $swaggerUiEnabled;

    private bool $reDocEnabled;

    private bool $graphqlEnabled;

    private bool $graphiQlEnabled;

    private bool $graphQlPlaygroundEnabled;

    private array $swaggerVersions;

    /**
     * @param int[] $swaggerVersions
     */
    public function __construct(ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory, ResourceMetadataFactoryInterface $resourceMetadataFactory, NormalizerInterface $normalizer, TwigEnvironment $twig, UrlGeneratorInterface $urlGenerator, string $title = '', string $description = '', string $version = '', $formats = [], $oauthEnabled = false, $oauthClientId = '', $oauthClientSecret = '', $oauthType = '', $oauthFlow = '', $oauthTokenUrl = '', $oauthAuthorizationUrl = '', $oauthScopes = [], bool $showWebby = true, bool $swaggerUiEnabled = false, bool $reDocEnabled = false, bool $graphqlEnabled = false, bool $graphiQlEnabled = false, bool $graphQlPlaygroundEnabled = false, array $swaggerVersions = [2, 3])
    {
        $this->resourceNameCollectionFactory = $resourceNameCollectionFactory;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->normalizer = $normalizer;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->title = $title;
        $this->showWebby = $showWebby;
        $this->description = $description;
        $this->version = $version;
        $this->oauthEnabled = $oauthEnabled;
        $this->oauthClientId = $oauthClientId;
        $this->oauthClientSecret = $oauthClientSecret;
        $this->oauthType = $oauthType;
        $this->oauthFlow = $oauthFlow;
        $this->oauthTokenUrl = $oauthTokenUrl;
        $this->oauthAuthorizationUrl = $oauthAuthorizationUrl;
        $this->oauthScopes = $oauthScopes;
        $this->swaggerUiEnabled = $swaggerUiEnabled;
        $this->reDocEnabled = $reDocEnabled;
        $this->graphqlEnabled = $graphqlEnabled;
        $this->graphiQlEnabled = $graphiQlEnabled;
        $this->graphQlPlaygroundEnabled = $graphQlPlaygroundEnabled;
        $this->swaggerVersions = $swaggerVersions;

        if (\is_array($formats)) {
            $this->formats = $formats;

            return;
        }

        @trigger_error(sprintf('Passing an array or an instance of "%s" as 5th parameter of the constructor of "%s" is deprecated since API Platform 2.5, pass an array instead', FormatsProviderInterface::class, __CLASS__), \E_USER_DEPRECATED);
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
            'url' => $this->urlGenerator->generate('api_doc', ['_format' => 'json'], $this->urlGenerator::ABSOLUTE_URL),
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
