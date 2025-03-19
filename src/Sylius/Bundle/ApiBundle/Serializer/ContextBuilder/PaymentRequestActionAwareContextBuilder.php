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

namespace Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Attribute\PaymentRequestActionAware;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class PaymentRequestActionAwareContextBuilder extends AbstractInputContextBuilder
{
    private ?Request $request = null;

    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        string $attributeClass,
        string $defaultConstructorArgumentName,
        private readonly DefaultActionProviderInterface $defaultActionProvider,
    ) {
        parent::__construct($decoratedContextBuilder, $attributeClass, $defaultConstructorArgumentName);
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $this->request = $request;

        return parent::createFromRequest($request, $normalization, $extractedAttributes);
    }

    protected function supports(Request $request, array $context, ?array $extractedAttributes): bool
    {
        $data = $request->toArray();

        return null === ($data[PaymentRequestActionAware::DEFAULT_ARGUMENT_NAME] ?? null) && null !== ($data['paymentMethodCode'] ?? null);
    }

    protected function resolveValue(array $context, ?array $extractedAttributes): mixed
    {
        $data = $this->request->toArray();

        return $this->defaultActionProvider->getActionFromPaymentMethodCode($data['paymentMethodCode']);
    }
}
