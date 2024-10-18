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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
readonly class BreadcrumbComponent
{
    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private RequestStack $requestStack,
        private TaxonRepositoryInterface $taxonRepository,
        private LocaleContextInterface $localeContext,
    ) {
    }

    #[ExposeInTemplate('taxon')]
    public function taxon(): TaxonInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $taxonSlug = $request->attributes->get('slug');

        if (null === $taxonSlug) {
            throw new \InvalidArgumentException('Taxon slug is required to render breadcrumb.');
        }

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBySlug($taxonSlug, $this->localeContext->getLocaleCode());

        return $taxon;
    }
}
