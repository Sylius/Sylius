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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\Exception\TaxonNotFoundException as ApiTaxonNotFoundException;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Exception\TaxonNotFoundException;
use Sylius\Component\Taxonomy\Provider\TaxonTreeProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<TaxonInterface> */
abstract class AbstractTaxonTreeProvider implements ProviderInterface
{
    public function __construct(
        protected readonly TaxonTreeProviderInterface $taxonTreeProvider,
        protected readonly SectionProviderInterface $sectionProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        Assert::true(is_a($operation->getClass(), TaxonInterface::class, true));
        Assert::isInstanceOf($operation, Get::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        try {
            return $this->getTreeResources(
                $this->getCodeVariable($uriVariables),
                $this->resolveIncludeRoot($context),
            );
        } catch (TaxonNotFoundException $exception) {
            throw new ApiTaxonNotFoundException(previous: $exception);
        }
    }

    abstract public function getTreeResources(string $code, bool $includeRoot = false): Collection;

    /** @param array<string|mixed> $uriVariables */
    protected function getCodeVariable(array $uriVariables): string
    {
        if (false === isset($uriVariables['code']) || '' === $uriVariables['code']) {
            throw new BadRequestHttpException(
                'The "code" URI variable is required to retrieve taxon tree resources.',
            );
        }

        return (string) $uriVariables['code'];
    }

    /** @param string[] $context */
    protected function resolveIncludeRoot(array $context): bool
    {
        $request = $context['request'] ?? null;
        if (false === $request instanceof Request) {
            return false;
        }

        return $request->query->getBoolean('includeRoot');
    }
}
