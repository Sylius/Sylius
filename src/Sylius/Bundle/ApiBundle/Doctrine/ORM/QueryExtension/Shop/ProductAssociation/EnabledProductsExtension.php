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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductAssociation;

use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Webmozart\Assert\Assert;

final readonly class EnabledProductsExtension implements QueryItemExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    /**
     * @param array<array-key, mixed> $identifiers
     * @param array<array-key, mixed> $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductAssociationInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $enabled = $queryNameGenerator->generateParameterName('enabled');
        $channel = $queryNameGenerator->generateParameterName('channel');

        $queryBuilder->addSelect('associatedProduct');
        $queryBuilder->leftJoin(sprintf('%s.associatedProducts', $rootAlias), 'associatedProduct', 'WITH', sprintf('associatedProduct.enabled = :%s', $enabled));
        $queryBuilder->innerJoin('associatedProduct.channels', 'channel', 'WITH', sprintf('channel = :%s', $channel));
        $queryBuilder->setParameter($enabled, true);
        $queryBuilder->setParameter($channel, $context[ContextKeys::CHANNEL]);
    }
}
