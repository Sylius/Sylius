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

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/**
 * Renders the product list as a live component.
 * Only reachable when the "sylius_shop.product_grid.live" option is enabled.
 *
 * @experimental
 */
final readonly class ProductListController
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(
        private Environment $twig,
        private TaxonRepositoryInterface $taxonRepository,
        private LocaleContextInterface $localeContext,
    ) {
    }

    public function __invoke(?string $slug = null): Response
    {
        $taxon = null;

        if (null !== $slug) {
            /** @var TaxonInterface|null $taxon */
            $taxon = $this->taxonRepository->findOneBySlug($slug, $this->localeContext->getLocaleCode());

            if (null === $taxon) {
                throw new NotFoundHttpException(sprintf('The taxon with slug "%s" has not been found.', $slug));
            }
        }

        return new Response($this->twig->render('@SyliusShop/product/live_index.html.twig', [
            'taxon' => $taxon,
        ]));
    }
}
