<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Symfony\EventListener;

use ApiPlatform\Api\IriConverterInterface as LegacyIriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface as LegacyResourceClassResolverInterface;
use ApiPlatform\Api\UriVariablesConverterInterface as LegacyUriVariablesConverterInterface;
use ApiPlatform\Exception\InvalidIdentifierException;
use ApiPlatform\Metadata\Error;
use ApiPlatform\Metadata\Exception\InvalidUriVariableException;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Metadata\UriVariablesConverterInterface;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use ApiPlatform\Metadata\Util\CloneTrait;
use ApiPlatform\State\CallableProcessor;
use ApiPlatform\State\Processor\WriteProcessor;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\UriVariablesResolverTrait;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This class is being overridden due to an issue with the clone method in the Adjustment class.
 * The fix has been merged in PR #6367 (https://github.com/api-platform/core/pull/6367), and we are waiting for the new version of ApiPlatform to be released.
 *
 * Bridges persistence and the API system.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Baptiste Meyer <baptiste.meyer@gmail.com>
 */
final class WriteListener
{
    use ClassInfoTrait;
    use CloneTrait;
    use OperationRequestInitiatorTrait;
    use UriVariablesResolverTrait;

    private LegacyIriConverterInterface|IriConverterInterface|null $iriConverter = null;

    /**
     * @param ProcessorInterface<mixed, mixed> $processor
     */
    public function __construct(
        private readonly ProcessorInterface $processor,
        LegacyIriConverterInterface|IriConverterInterface|ResourceMetadataCollectionFactoryInterface|null $iriConverter = null,
        private readonly ResourceClassResolverInterface|LegacyResourceClassResolverInterface|null $resourceClassResolver = null,
        ?ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory = null,
        LegacyUriVariablesConverterInterface|UriVariablesConverterInterface|null $uriVariablesConverter = null,
    ) {
        $this->uriVariablesConverter = $uriVariablesConverter;

        if ($processor instanceof CallableProcessor) {
            trigger_deprecation('api-platform/core', '3.3', 'Use a "%s" as first argument in "%s" instead of "%s".', WriteProcessor::class, self::class, $processor::class);
        }

        if ($iriConverter instanceof ResourceMetadataCollectionFactoryInterface) {
            $resourceMetadataCollectionFactory = $iriConverter;
        } else {
            $this->iriConverter = $iriConverter;
            trigger_deprecation('api-platform/core', '3.3', 'Use a "%s" as second argument in "%s" instead of "%s".', ResourceMetadataCollectionFactoryInterface::class, self::class, IriConverterInterface::class);
        }

        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    /**
     * Persists, updates or delete data return by the controller if applicable.
     */
    public function onKernelView(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();
        $operation = $this->initializeOperation($request);

        if (!($attributes = RequestAttributesExtractor::extractAttributes($request)) || !$attributes['persist']) {
            return;
        }

        if ($operation && (!$this->processor instanceof CallableProcessor && !$this->iriConverter)) {
            if (null === $operation->canWrite()) {
                $operation = $operation->withWrite(!$request->isMethodSafe());
            }

            $uriVariables = $request->attributes->get('_api_uri_variables') ?? [];
            if (!$uriVariables && !$operation instanceof Error && $operation instanceof HttpOperation) {
                try {
                    $uriVariables = $this->getOperationUriVariables($operation, $request->attributes->all(), $operation->getClass());
                } catch (InvalidIdentifierException|InvalidUriVariableException $e) {
                    throw new NotFoundHttpException('Invalid identifier value or configuration.', $e);
                }
            }

            // $request->attributes->set('original_data', $this->clone($controllerResult));
            $data = $this->processor->process($controllerResult, $operation, $uriVariables, [
                'request' => $request,
                'uri_variables' => $uriVariables,
                'resource_class' => $operation->getClass(),
                'previous_data' => false === $operation->canRead() ? null : $request->attributes->get('previous_data'),
            ]);

            if ($data) {
                $request->attributes->set('original_data', $data);
            }

            $event->setControllerResult($data);

            return;
        }

        // API Platform 3.2 has a MainController where everything is handled by processors/providers
        if ('api_platform.symfony.main_controller' === $operation?->getController() || $request->attributes->get('_api_platform_disable_listeners')) {
            return;
        }

        if (
            $controllerResult instanceof Response
            || $request->isMethodSafe()
            || !($attributes = RequestAttributesExtractor::extractAttributes($request))
        ) {
            return;
        }

        if (!$attributes['persist'] || !($operation?->canWrite() ?? true)) {
            return;
        }

        if (!$operation?->getProcessor()) {
            return;
        }

        $context = [
            'operation' => $operation,
            'resource_class' => $attributes['resource_class'],
            'previous_data' => $attributes['previous_data'] ?? null,
        ];

        try {
            $uriVariables = $this->getOperationUriVariables($operation, $request->attributes->all(), $attributes['resource_class']);
        } catch (InvalidIdentifierException $e) {
            throw new NotFoundHttpException('Invalid identifier value or configuration.', $e);
        }

        switch ($request->getMethod()) {
            case 'PUT':
            case 'PATCH':
            case 'POST':
                $persistResult = $this->processor->process($controllerResult, $operation, $uriVariables, $context);

                if ($persistResult) {
                    $controllerResult = $persistResult;
                    $event->setControllerResult($controllerResult);
                }

                if ($controllerResult instanceof Response) {
                    break;
                }

                $outputMetadata = $operation->getOutput() ?? ['class' => $attributes['resource_class']];
                $hasOutput = \is_array($outputMetadata) && \array_key_exists('class', $outputMetadata) && null !== $outputMetadata['class'];
                if (!$hasOutput) {
                    break;
                }

                if ($this->resourceClassResolver->isResourceClass($this->getObjectClass($controllerResult))) {
                    $request->attributes->set('_api_write_item_iri', $this->iriConverter->getIriFromResource($controllerResult));
                }

                break;
            case 'DELETE':
                $this->processor->process($controllerResult, $operation, $uriVariables, $context);
                $event->setControllerResult(null);
                break;
        }
    }
}
