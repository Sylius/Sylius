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

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class TaxonCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TaxonRepositoryInterface $taxonRepository, TokenStorageInterface $tokenStorage)
    {
        $this->taxonRepository = $taxonRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, TaxonInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channelMenuTaxon = $context[ContextKeys::CHANNEL]->getMenuTaxon();

        $user = $this->tokenStorage->getToken()->getUser();
        if ($user !== 'anon.' && in_array('ROLE_API_ACCESS', $user->getRoles())) {
            return $this->taxonRepository->findAll();
        }

        return $this->taxonRepository->findChildrenByChannelMenuTaxon($channelMenuTaxon);
    }
}
