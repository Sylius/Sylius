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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class TaxonCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    /** @var PaginationExtension */
    private $paginationExtension;

    /** @var UserContextInterface */
    private $userContext;

    /**
     * @var iterable
     *
     * @see QueryCollectionExtensionInterface
     */
    private $collectionExtensions;

    /** @var QueryNameGeneratorInterface */
    private $queryNameGenerator;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        PaginationExtension $paginationExtension,
        UserContextInterface $userContext,
        QueryNameGeneratorInterface $queryNameGenerator,
        iterable $collectionExtensions
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->paginationExtension = $paginationExtension;
        $this->userContext = $userContext;
        $this->queryNameGenerator = $queryNameGenerator;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, TaxonInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channelMenuTaxon = $context[ContextKeys::CHANNEL]->getMenuTaxon();

        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles())) {
            return $this->taxonRepository->findAll();
        }

        $queryBuilder = $this->taxonRepository->createChildrenByChannelMenuTaxonQueryBuilder(
            $channelMenuTaxon
        );
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $this->queryNameGenerator, $resourceClass, $operationName, $context);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $this->paginationExtension->getResult(
            $queryBuilder,
            $resourceClass,
            $operationName,
            $context
        );
    }
}
