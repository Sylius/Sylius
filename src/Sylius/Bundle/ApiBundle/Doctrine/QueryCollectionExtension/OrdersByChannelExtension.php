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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Webmozart\Assert\Assert;

final class OrdersByChannelExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface) {
            return;
        }

        if (!$user instanceof ShopUserInterface) {
            throw new AccessDeniedException();
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channel = $context[ContextKeys::CHANNEL];

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $channelParameterName = $queryNameGenerator->generateParameterName('channel');

        $queryBuilder
            ->andWhere(sprintf('%s.channel = :%s', $rootAlias, $channelParameterName))
            ->setParameter($channelParameterName, $channel)
        ;
    }
}
