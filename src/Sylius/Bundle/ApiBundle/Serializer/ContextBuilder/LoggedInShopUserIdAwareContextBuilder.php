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

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;

final class LoggedInShopUserIdAwareContextBuilder extends AbstractInputContextBuilder
{
    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        string $attributeClass,
        string $defaultConstructorArgumentName,
        private readonly UserContextInterface $userContext,
    ) {
        parent::__construct($decoratedContextBuilder, $attributeClass, $defaultConstructorArgumentName);
    }

    protected function supportsClass(string $class): bool
    {
        return is_a($class, ShopUserIdAwareInterface::class, true);
    }

    protected function supports(Request $request, array $context, ?array $extractedAttributes): bool
    {
        return $this->getShopUser() !== null;
    }

    protected function resolveValue(array $context, ?array $extractedAttributes): mixed
    {
        return $this->getShopUser()->getId();
    }

    private function getShopUser(): ?ShopUserInterface
    {
        $user = $this->userContext->getUser();

        return $user instanceof ShopUserInterface ? $user : null;
    }
}
