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

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailIfNotSetAware;
use Sylius\Bundle\ApiBundle\Command\LoggedInCustomerEmailIfNotSetAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Webmozart\Assert\Assert;

/** @experimental */
final readonly class LoggedInCustomerEmailIfNotSetAwareContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decoratedContextBuilder,
        private UserContextInterface $userContext,
    ) {
    }

    /**
     * @param array<string>|null $extractedAttributes
     *
     * @return array<string, mixed>
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decoratedContextBuilder->createFromRequest($request, $normalization, $extractedAttributes);
        $inputClass = $this->getInputClassFromContext($context);

        if ($inputClass === null || !$this->isCustomerEmailIfNotSetAware($inputClass)) {
            return $context;
        }

        if (array_key_exists('email', $request->toArray())) {
            return $context;
        }

        /** @var CustomerInterface|null $customer */
        $customer = $this->getCustomer();

        if ($customer === null) {
            return $context;
        }

        $constructorArgumentName = $this->getConstructorArgumentName($inputClass) ?? 'email';

        if (isset($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass]) && is_array($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass])) {
            $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = array_merge($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass], [$constructorArgumentName => $customer->getEmail()]);
        } else {
            $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = [$constructorArgumentName => $customer->getEmail()];
        }

        return $context;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function getInputClassFromContext(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }

    private function getConstructorArgumentName(string $class): ?string
    {
        $classReflection = new \ReflectionClass($class);
        $attributes = $classReflection->getAttributes(LoggedInCustomerEmailIfNotSetAware::class);

        if (count($attributes) === 0) {
            return null;
        }

        /** @var LoggedInCustomerEmailIfNotSetAware $loggedInCustomerEmailIfNotSetAwareAttribute */
        $loggedInCustomerEmailIfNotSetAwareAttribute = $attributes[0]->newInstance();

        return $loggedInCustomerEmailIfNotSetAwareAttribute->constructorArgumentName;
    }

    private function isCustomerEmailIfNotSetAware(string $inputClass): bool
    {
        return is_a($inputClass, LoggedInCustomerEmailIfNotSetAwareInterface::class, true);
    }

    private function getCustomer(): ?CustomerInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->userContext->getUser();
        if ($user instanceof ShopUserInterface) {
            $customer = $user->getCustomer();
            Assert::nullOrIsInstanceOf($customer, CustomerInterface::class);

            return $customer;
        }

        return null;
    }
}
