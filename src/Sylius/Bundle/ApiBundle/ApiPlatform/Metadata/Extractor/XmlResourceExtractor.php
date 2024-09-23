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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Extractor;

use ApiPlatform\Elasticsearch\State\Options;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Extractor\AbstractResourceExtractor;
use ApiPlatform\Metadata\Extractor\ResourceExtractorTrait;
use ApiPlatform\Metadata\Extractor\XmlPropertyExtractor;
use ApiPlatform\Metadata\Extractor\XmlResourceExtractor as BaseXmlResourceExtractor;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\HeaderParameter;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\ExternalDocumentation;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\State\OptionsInterface;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\WebLink\Link;

/**
 * This class overrides the {@see \ApiPlatform\Metadata\Extractor\XmlResourceExtractor}
 * to remove the setting of an incorrect class value when passing it as a parameter
 *
 * @internal
 */
final class XmlResourceExtractor extends AbstractResourceExtractor
{
    use ResourceExtractorTrait;

    /**
     * {@inheritdoc}
     */
    protected function extractPath(string $path): void
    {
        try {
            /** @var \SimpleXMLElement $xml */
            $xml = simplexml_import_dom(XmlUtils::loadFile($path, BaseXmlResourceExtractor::SCHEMA));
        } catch (\InvalidArgumentException $e) {
            // Ensure it's not a resource
            try {
                simplexml_import_dom(XmlUtils::loadFile($path, XmlPropertyExtractor::SCHEMA));
            } catch (\InvalidArgumentException) {
                throw new InvalidArgumentException(\sprintf('Error while parsing %s: %s', $path, $e->getMessage()), $e->getCode(), $e);
            }

            // It's a property: ignore error
            return;
        }

        foreach ($xml->resource as $resource) {
            $base = $this->buildExtendedBase($resource);
            $this->resources[$this->resolve((string) $resource['class'])][] = array_merge($base, [
                'operations' => $this->buildOperations($resource, $base),
                'graphQlOperations' => $this->buildGraphQlOperations($resource, $base),
            ]);
        }
    }

    private function buildExtendedBase(\SimpleXMLElement $resource): array
    {
        return array_merge($this->buildBase($resource), [
            'uriTemplate' => $this->phpize($resource, 'uriTemplate', 'string'),
            'routePrefix' => $this->phpize($resource, 'routePrefix', 'string'),
            'stateless' => $this->phpize($resource, 'stateless', 'bool'),
            'sunset' => $this->phpize($resource, 'sunset', 'string'),
            'acceptPatch' => $this->phpize($resource, 'acceptPatch', 'string'),
            'status' => $this->phpize($resource, 'status', 'integer'),
            'host' => $this->phpize($resource, 'host', 'string'),
            'condition' => $this->phpize($resource, 'condition', 'string'),
            'controller' => $this->phpize($resource, 'controller', 'string'),
            'types' => $this->buildArrayValue($resource, 'type'),
            'formats' => $this->buildFormats($resource, 'formats'),
            'inputFormats' => $this->buildFormats($resource, 'inputFormats'),
            'outputFormats' => $this->buildFormats($resource, 'outputFormats'),
            'uriVariables' => $this->buildUriVariables($resource),
            'defaults' => isset($resource->defaults->values) ? $this->buildValues($resource->defaults->values) : null,
            'requirements' => $this->buildRequirements($resource),
            'options' => isset($resource->options->values) ? $this->buildValues($resource->options->values) : null,
            'schemes' => $this->buildArrayValue($resource, 'scheme'),
            'cacheHeaders' => $this->buildCacheHeaders($resource),
            'hydraContext' => isset($resource->hydraContext->values) ? $this->buildValues($resource->hydraContext->values) : null,
            'openapiContext' => isset($resource->openapiContext->values) ? $this->buildValues($resource->openapiContext->values) : null, // TODO Remove in 4.0
            'openapi' => $this->buildOpenapi($resource),
            'paginationViaCursor' => $this->buildPaginationViaCursor($resource),
            'exceptionToStatus' => $this->buildExceptionToStatus($resource),
            'queryParameterValidationEnabled' => $this->phpize($resource, 'queryParameterValidationEnabled', 'bool'),
            'stateOptions' => $this->buildStateOptions($resource),
            'links' => $this->buildLinks($resource),
            'headers' => $this->buildHeaders($resource),
            'parameters' => $this->buildParameters($resource),
        ]);
    }

    private function buildBase(\SimpleXMLElement $resource): array
    {
        return [
            'shortName' => $this->phpize($resource, 'shortName', 'string'),
            'description' => $this->phpize($resource, 'description', 'string'),
            'urlGenerationStrategy' => $this->phpize($resource, 'urlGenerationStrategy', 'integer'),
            'deprecationReason' => $this->phpize($resource, 'deprecationReason', 'string'),
            'elasticsearch' => $this->phpize($resource, 'elasticsearch', 'bool'),
            'messenger' => $this->phpize($resource, 'messenger', 'bool|string'),
            'mercure' => $this->buildMercure($resource),
            'input' => $this->phpize($resource, 'input', 'bool|string'),
            'output' => $this->phpize($resource, 'output', 'bool|string'),
            'fetchPartial' => $this->phpize($resource, 'fetchPartial', 'bool'),
            'forceEager' => $this->phpize($resource, 'forceEager', 'bool'),
            'paginationClientEnabled' => $this->phpize($resource, 'paginationClientEnabled', 'bool'),
            'paginationClientItemsPerPage' => $this->phpize($resource, 'paginationClientItemsPerPage', 'bool'),
            'paginationClientPartial' => $this->phpize($resource, 'paginationClientPartial', 'bool'),
            'paginationEnabled' => $this->phpize($resource, 'paginationEnabled', 'bool'),
            'paginationFetchJoinCollection' => $this->phpize($resource, 'paginationFetchJoinCollection', 'bool'),
            'paginationUseOutputWalkers' => $this->phpize($resource, 'paginationUseOutputWalkers', 'bool'),
            'paginationItemsPerPage' => $this->phpize($resource, 'paginationItemsPerPage', 'integer'),
            'paginationMaximumItemsPerPage' => $this->phpize($resource, 'paginationMaximumItemsPerPage', 'integer'),
            'paginationPartial' => $this->phpize($resource, 'paginationPartial', 'bool'),
            'paginationType' => $this->phpize($resource, 'paginationType', 'string'),
            'processor' => $this->phpize($resource, 'processor', 'string'),
            'provider' => $this->phpize($resource, 'provider', 'string'),
            'security' => $this->phpize($resource, 'security', 'string'),
            'securityMessage' => $this->phpize($resource, 'securityMessage', 'string'),
            'securityPostDenormalize' => $this->phpize($resource, 'securityPostDenormalize', 'string'),
            'securityPostDenormalizeMessage' => $this->phpize($resource, 'securityPostDenormalizeMessage', 'string'),
            'securityPostValidation' => $this->phpize($resource, 'securityPostValidation', 'string'),
            'securityPostValidationMessage' => $this->phpize($resource, 'securityPostValidationMessage', 'string'),
            'normalizationContext' => isset($resource->normalizationContext->values) ? $this->buildValues($resource->normalizationContext->values) : null,
            'denormalizationContext' => isset($resource->denormalizationContext->values) ? $this->buildValues($resource->denormalizationContext->values) : null,
            'collectDenormalizationErrors' => $this->phpize($resource, 'collectDenormalizationErrors', 'bool'),
            'validationContext' => isset($resource->validationContext->values) ? $this->buildValues($resource->validationContext->values) : null,
            'filters' => $this->buildArrayValue($resource, 'filter'),
            'order' => isset($resource->order->values) ? $this->buildValues($resource->order->values) : null,
            'extraProperties' => $this->buildExtraProperties($resource, 'extraProperties'),
            'read' => $this->phpize($resource, 'read', 'bool'),
            'write' => $this->phpize($resource, 'write', 'bool'),
        ];
    }

    private function buildFormats(\SimpleXMLElement $resource, string $key): ?array
    {
        if (!isset($resource->{$key}->format)) {
            return null;
        }

        $data = [];
        foreach ($resource->{$key}->format as $format) {
            if (isset($format['name'])) {
                $data[(string) $format['name']] = (string) $format;
                continue;
            }

            $data[] = (string) $format;
        }

        return $data;
    }

    private function buildOpenapi(\SimpleXMLElement $resource): bool|OpenApiOperation|null
    {
        if (!isset($resource->openapi) && !isset($resource['openapi'])) {
            return null;
        }

        if (isset($resource['openapi']) && \in_array((string) $resource['openapi'], ['1', '0', 'true', 'false'], true)) {
            return $this->phpize($resource, 'openapi', 'bool');
        }

        $openapi = $resource->openapi;
        $data = [];
        $attributes = $openapi->attributes();
        foreach ($attributes as $attribute) {
            $data[$attribute->getName()] = $this->phpize($attributes, 'deprecated', 'deprecated' === $attribute->getName() ? 'bool' : 'string');
        }

        $data['tags'] = $this->buildArrayValue($resource, 'tag');

        if (isset($openapi->responses->response)) {
            foreach ($openapi->responses->response as $response) {
                $data['responses'][(string) $response->attributes()->status] = [
                    'description' => $this->phpize($response, 'description', 'string'),
                    'content' => isset($response->content->values) ? $this->buildValues($response->content->values) : null,
                    'headers' => isset($response->headers->values) ? $this->buildValues($response->headers->values) : null,
                    'links' => isset($response->links->values) ? $this->buildValues($response->links->values) : null,
                ];
            }
        }

        $data['externalDocs'] = isset($openapi->externalDocs) ? new ExternalDocumentation(
            description: $this->phpize($resource, 'description', 'string'),
            url: $this->phpize($resource, 'url', 'string'),
        ) : null;

        if (isset($openapi->parameters->parameter)) {
            foreach ($openapi->parameters->parameter as $parameter) {
                $data['parameters'][(string) $parameter->attributes()->name] = new OpenApiParameter(
                    name: $this->phpize($parameter, 'name', 'string'),
                    in: $this->phpize($parameter, 'in', 'string'),
                    description: $this->phpize($parameter, 'description', 'string'),
                    required: $this->phpize($parameter, 'required', 'bool'),
                    deprecated: $this->phpize($parameter, 'deprecated', 'bool'),
                    allowEmptyValue: $this->phpize($parameter, 'allowEmptyValue', 'bool'),
                    schema: isset($parameter->schema->values) ? $this->buildValues($parameter->schema->values) : null,
                    style: $this->phpize($parameter, 'style', 'string'),
                    explode: $this->phpize($parameter, 'explode', 'bool'),
                    allowReserved: $this->phpize($parameter, 'allowReserved', 'bool'),
                    example: $this->phpize($parameter, 'example', 'string'),
                    examples: isset($parameter->examples->values) ? new \ArrayObject($this->buildValues($parameter->examples->values)) : null,
                    content: isset($parameter->content->values) ? new \ArrayObject($this->buildValues($parameter->content->values)) : null,
                );
            }
        }
        $data['requestBody'] = isset($openapi->requestBody) ? new RequestBody(
            description: $this->phpize($openapi->requestBody, 'description', 'string'),
            content: isset($openapi->requestBody->content->values) ? new \ArrayObject($this->buildValues($openapi->requestBody->content->values)) : null,
            required: $this->phpize($openapi->requestBody, 'required', 'bool'),
        ) : null;

        $data['callbacks'] = isset($openapi->callbacks->values) ? new \ArrayObject($this->buildValues($openapi->callbacks->values)) : null;

        $data['security'] = isset($openapi->security->values) ? $this->buildValues($openapi->security->values) : null;

        if (isset($openapi->servers->server)) {
            foreach ($openapi->servers->server as $server) {
                $data['servers'][] = [
                    'description' => $this->phpize($server, 'description', 'string'),
                    'url' => $this->phpize($server, 'url', 'string'),
                    'variables' => isset($server->variables->values) ? $this->buildValues($server->variables->values) : null,
                ];
            }
        }

        $data['extensionProperties'] = isset($openapi->extensionProperties->values) ? $this->buildValues($openapi->extensionProperties->values) : null;

        foreach ($data as $key => $value) {
            if (null === $value) {
                unset($data[$key]);
            }
        }

        return new OpenApiOperation(...$data);
    }

    private function buildUriVariables(\SimpleXMLElement $resource): ?array
    {
        if (!isset($resource->uriVariables->uriVariable)) {
            return null;
        }

        $uriVariables = [];
        foreach ($resource->uriVariables->uriVariable as $data) {
            $parameterName = (string) $data['parameterName'];
            if (1 === (null === $data->attributes() ? 0 : \count($data->attributes()))) {
                $uriVariables[$parameterName] = $parameterName;
                continue;
            }

            if ($fromProperty = $this->phpize($data, 'fromProperty', 'string')) {
                $uriVariables[$parameterName]['from_property'] = $fromProperty;
            }
            if ($toProperty = $this->phpize($data, 'toProperty', 'string')) {
                $uriVariables[$parameterName]['to_property'] = $toProperty;
            }
            if ($fromClass = $this->resolve($this->phpize($data, 'fromClass', 'string'))) {
                $uriVariables[$parameterName]['from_class'] = $fromClass;
            }
            if ($toClass = $this->resolve($this->phpize($data, 'toClass', 'string'))) {
                $uriVariables[$parameterName]['to_class'] = $toClass;
            }
            if (isset($data->identifiers->values)) {
                $uriVariables[$parameterName]['identifiers'] = $this->buildValues($data->identifiers->values);
            }
            if (null !== ($compositeIdentifier = $this->phpize($data, 'compositeIdentifier', 'bool'))) {
                $uriVariables[$parameterName]['composite_identifier'] = $compositeIdentifier;
            }
        }

        return $uriVariables;
    }

    private function buildCacheHeaders(\SimpleXMLElement $resource): ?array
    {
        if (!isset($resource->cacheHeaders->cacheHeader)) {
            return null;
        }

        $data = [];
        foreach ($resource->cacheHeaders->cacheHeader as $cacheHeader) {
            if (isset($cacheHeader->values->value)) {
                $data[(string) $cacheHeader['name']] = $this->buildValues($cacheHeader->values);
                continue;
            }

            $data[(string) $cacheHeader['name']] = (string) $cacheHeader;
        }

        return $data;
    }

    private function buildRequirements(\SimpleXMLElement $resource): ?array
    {
        if (!isset($resource->requirements->requirement)) {
            return null;
        }

        $data = [];
        foreach ($resource->requirements->requirement as $requirement) {
            $data[(string) $requirement->attributes()->property] = (string) $requirement;
        }

        return $data;
    }

    private function buildMercure(\SimpleXMLElement $resource): array|bool|null
    {
        if (!isset($resource->mercure)) {
            return null;
        }

        if (null !== $resource->mercure->attributes()->private) {
            return ['private' => $this->phpize($resource->mercure->attributes(), 'private', 'bool')];
        }

        return true;
    }

    private function buildPaginationViaCursor(\SimpleXMLElement $resource): ?array
    {
        if (!isset($resource->paginationViaCursor->paginationField)) {
            return null;
        }

        $data = [];
        foreach ($resource->paginationViaCursor->paginationField as $paginationField) {
            $data[(string) $paginationField['field']] = (string) $paginationField['direction'];
        }

        return $data;
    }

    private function buildExceptionToStatus(\SimpleXMLElement $resource): ?array
    {
        if (!isset($resource->exceptionToStatus->exception)) {
            return null;
        }

        $data = [];
        foreach ($resource->exceptionToStatus->exception as $exception) {
            $data[(string) $exception['class']] = (int) $exception['statusCode'];
        }

        return $data;
    }

    private function buildExtraProperties(\SimpleXMLElement $resource, ?string $key = null): ?array
    {
        if (null !== $key) {
            if (!isset($resource->{$key})) {
                return null;
            }

            $resource = $resource->{$key};
        }

        return $this->buildValues($resource->values);
    }

    private function buildOperations(\SimpleXMLElement $resource, array $root): ?array
    {
        if (!isset($resource->operations->operation)) {
            return null;
        }

        $data = [];
        foreach ($resource->operations->operation as $operation) {
            $datum = $this->buildExtendedBase($operation);
            foreach ($datum as $key => $value) {
                if (null === $value) {
                    $datum[$key] = $root[$key];
                }
            }

            if (\in_array((string) $operation['class'], [GetCollection::class, Post::class], true)) {
                $datum['itemUriTemplate'] = $this->phpize($operation, 'itemUriTemplate', 'string');
            } elseif (isset($operation['itemUriTemplate'])) {
                throw new InvalidArgumentException(\sprintf('"itemUriTemplate" option is not allowed on a %s operation.', $operation['class']));
            }

            $data[] = array_merge($datum, [
                'collection' => $this->phpize($operation, 'collection', 'bool'),
                'class' => (string) $operation['class'],
                'method' => $this->phpize($operation, 'method', 'string'),
                'read' => $this->phpize($operation, 'read', 'bool'),
                'deserialize' => $this->phpize($operation, 'deserialize', 'bool'),
                'validate' => $this->phpize($operation, 'validate', 'bool'),
                'write' => $this->phpize($operation, 'write', 'bool'),
                'serialize' => $this->phpize($operation, 'serialize', 'bool'),
                'queryParameterValidate' => $this->phpize($operation, 'queryParameterValidate', 'bool'),
                'priority' => $this->phpize($operation, 'priority', 'integer'),
                'name' => $this->phpize($operation, 'name', 'string'),
                'routeName' => $this->phpize($operation, 'routeName', 'string'),
            ]);
        }

        return $data;
    }

    private function buildGraphQlOperations(\SimpleXMLElement $resource, array $root): ?array
    {
        if (!isset($resource->graphQlOperations->graphQlOperation)) {
            return null;
        }

        $data = [];
        foreach ($resource->graphQlOperations->graphQlOperation as $operation) {
            $datum = $this->buildBase($operation);
            foreach ($datum as $key => $value) {
                if (null === $value) {
                    $datum[$key] = $root[$key];
                }
            }

            $data[] = array_merge($datum, [
                'resolver' => $this->phpize($operation, 'resolver', 'string'),
                'args' => $this->buildArgs($operation),
                'extraArgs' => $this->buildExtraArgs($operation),
                'class' => (string) $operation['class'],
                'read' => $this->phpize($operation, 'read', 'bool'),
                'deserialize' => $this->phpize($operation, 'deserialize', 'bool'),
                'validate' => $this->phpize($operation, 'validate', 'bool'),
                'write' => $this->phpize($operation, 'write', 'bool'),
                'serialize' => $this->phpize($operation, 'serialize', 'bool'),
                'priority' => $this->phpize($operation, 'priority', 'integer'),
                'name' => $this->phpize($operation, 'name', 'string'),
            ]);
        }

        return $data;
    }

    private function buildStateOptions(\SimpleXMLElement $resource): ?OptionsInterface
    {
        $stateOptions = $resource->stateOptions ?? null;
        if (!$stateOptions) {
            return null;
        }
        $elasticsearchOptions = $stateOptions->elasticsearchOptions ?? null;
        if ($elasticsearchOptions) {
            if (class_exists(Options::class)) {
                return new Options(
                    isset($elasticsearchOptions['index']) ? (string) $elasticsearchOptions['index'] : null,
                    isset($elasticsearchOptions['type']) ? (string) $elasticsearchOptions['type'] : null,
                );
            }
        }

        return null;
    }

    /**
     * @return Link[]
     */
    private function buildLinks(\SimpleXMLElement $resource): ?array
    {
        if (!$resource->links) {
            return null;
        }

        $links = [];
        foreach ($resource->links as $link) {
            $links[] = new Link(rel: (string) $link->link->attributes()->rel, href: (string) $link->link->attributes()->href);
        }

        return $links;
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(\SimpleXMLElement $resource): ?array
    {
        if (!$resource->headers) {
            return null;
        }

        $headers = [];
        foreach ($resource->headers as $header) {
            $headers[(string) $header->header->attributes()->key] = (string) $header->header->attributes()->value;
        }

        return $headers;
    }

    /**
     * @return array<string, \ApiPlatform\Metadata\Parameter>
     */
    private function buildParameters(\SimpleXMLElement $resource): ?array
    {
        if (!$resource->parameters) {
            return null;
        }

        $parameters = [];
        foreach ($resource->parameters->parameter as $parameter) {
            $key = (string) $parameter->attributes()->key;
            $cl = ('header' === (string) $parameter->attributes()->in) ? HeaderParameter::class : QueryParameter::class;
            $parameters[$key] = new $cl(
                key: $key,
                required: $this->phpize($parameter, 'required', 'bool'),
                schema: isset($parameter->schema->values) ? $this->buildValues($parameter->schema->values) : null,
                openApi: isset($parameter->openapi) ? new OpenApiParameter(
                    name: $this->phpize($parameter->openapi, 'name', 'string'),
                    in: $this->phpize($parameter->openapi, 'in', 'string'),
                    description: $this->phpize($parameter->openapi, 'description', 'string'),
                    required: $this->phpize($parameter->openapi, 'required', 'bool'),
                    deprecated: $this->phpize($parameter->openapi, 'deprecated', 'bool'),
                    allowEmptyValue: $this->phpize($parameter->openapi, 'allowEmptyValue', 'bool'),
                    schema: isset($parameter->openapi->schema->values) ? $this->buildValues($parameter->openapi->schema->values) : null,
                    style: $this->phpize($parameter->openapi, 'style', 'string'),
                    explode: $this->phpize($parameter->openapi, 'explode', 'bool'),
                    allowReserved: $this->phpize($parameter->openapi, 'allowReserved', 'bool'),
                    example: $this->phpize($parameter->openapi, 'example', 'string'),
                    examples: isset($parameter->openapi->examples->values) ? new \ArrayObject($this->buildValues($parameter->openapi->examples->values)) : null,
                    content: isset($parameter->openapi->content->values) ? new \ArrayObject($this->buildValues($parameter->openapi->content->values)) : null,
                ) : null,
                provider: $this->phpize($parameter, 'provider', 'string'),
                filter: $this->phpize($parameter, 'filter', 'string'),
                property: $this->phpize($parameter, 'property', 'string'),
                description: $this->phpize($parameter, 'description', 'string'),
                priority: $this->phpize($parameter, 'priority', 'integer'),
                extraProperties: $this->buildExtraProperties($parameter, 'extraProperties') ?? [],
            );
        }

        return $parameters;
    }
}
