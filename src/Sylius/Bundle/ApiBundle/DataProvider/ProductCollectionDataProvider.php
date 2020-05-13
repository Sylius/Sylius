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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var ProductRepositoryInterface|EntityRepository */
    private $productRepository;

    /** @var UserContextInterface */
    private $userContext;

    /** @var ContextAwareQueryCollectionExtensionInterface[] */
    private $collectionExtensions;

    /**
     * @param ContextAwareQueryCollectionExtensionInterface[] $collectionExtensions
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        UserContextInterface $userContext,
        iterable $collectionExtensions = []
    ) {
        $this->productRepository = $productRepository;
        $this->userContext = $userContext;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ProductInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $queryBuilder = $this->provideQueryBuilder($context);
        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);

            if (
                $extension instanceof ContextAwareQueryResultCollectionExtensionInterface &&
                $extension->supportsResult($resourceClass, $operationName, $context)
            ) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /** @psalm-suppress PossiblyUndefinedMethod */
    private function provideQueryBuilder(array $context): QueryBuilder
    {
        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles())) {
            return $this->productRepository->createQueryBuilder('o');
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        Assert::keyExists($context, ContextKeys::LOCALE_CODE);

        $channel = $context[ContextKeys::CHANNEL];
        $localeCode = $context[ContextKeys::LOCALE_CODE];

        return $this->productRepository->createListQueryBuilderByChannelAndLocaleCode($channel, $localeCode);
    }
}
