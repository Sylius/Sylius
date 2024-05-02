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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Webmozart\Assert\Assert;

final class EnabledProductInProductAssociationItemExtension implements QueryItemExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = [],
    ) {
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
