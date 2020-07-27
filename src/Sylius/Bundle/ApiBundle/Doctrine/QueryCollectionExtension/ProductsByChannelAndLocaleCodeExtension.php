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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Helper\UserContextHelperInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductsByChannelAndLocaleCodeExtension implements ContextAwareQueryCollectionExtensionInterface
{
    /** @var UserContextHelperInterface */
    private $userContextHelper;

    public function __construct(UserContextHelperInterface $userContextHelper)
    {
        $this->userContextHelper = $userContextHelper;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        if ($this->userContextHelper->hasAdminRoleApiAccess()) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        Assert::keyExists($context, ContextKeys::LOCALE_CODE);

        $channel = $context[ContextKeys::CHANNEL];
        $localeCode = $context[ContextKeys::LOCALE_CODE];

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->addSelect('translation')
            ->innerJoin(sprintf('%s.translations', $rootAlias), 'translation', 'WITH', 'translation.locale = :localeCode')
            ->andWhere(sprintf(':channel MEMBER OF %s.channels', $rootAlias))
            ->setParameter('channel', $channel)
            ->setParameter('localeCode', $localeCode)
        ;
    }
}
