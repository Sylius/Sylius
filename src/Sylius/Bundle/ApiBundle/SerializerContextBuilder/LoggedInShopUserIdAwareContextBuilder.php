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
use Sylius\Bundle\ApiBundle\Attribute\ShopUserIdAware;
use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/** @experimental */
final readonly class LoggedInShopUserIdAwareContextBuilder implements SerializerContextBuilderInterface
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

        if ($inputClass === null || !$this->isShopUserIdAware($inputClass)) {
            return $context;
        }

        $constructorArgumentName = $this->getConstructorArgumentName($inputClass) ?? 'shopUserId';

        $user = $this->getShopUser();
        if (null !== $user) {
            if (isset($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass]) && is_array($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass])) {
                $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = array_merge($context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass], [$constructorArgumentName => $user->getId()]);
            } else {
                $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = [$constructorArgumentName => $user->getId()];
            }
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

    private function isShopUserIdAware(string $inputClass): bool
    {
        return is_a($inputClass, ShopUserIdAwareInterface::class, true);
    }

    private function getConstructorArgumentName(string $class): ?string
    {
        $classReflection = new \ReflectionClass($class);
        $attributes = $classReflection->getAttributes(ShopUserIdAware::class);

        if (count($attributes) === 0) {
            return null;
        }

        /** @var ShopUserIdAware $shopUserIdAwareAttribute */
        $shopUserIdAwareAttribute = $attributes[0]->newInstance();

        return $shopUserIdAwareAttribute->constructorArgumentName;
    }

    private function getShopUser(): ?ShopUserInterface
    {
        $user = $this->userContext->getUser();

        return $user instanceof ShopUserInterface ? $user : null;
    }
}
