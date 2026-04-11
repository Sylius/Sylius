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
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class BreadcrumbComponent
{
    use HookableComponentTrait;

    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        protected RequestStack $requestStack,
        protected TaxonRepositoryInterface $taxonRepository,
        protected LocaleContextInterface $localeContext,
    ) {
    }

    #[ExposeInTemplate('taxon')]
    public function taxon(): TaxonInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (false === $request instanceof Request) {
            throw new \InvalidArgumentException('Request is required to render breadcrumb.');
        }

        $taxonSlug = $request->attributes->get('slug');

        if (null === $taxonSlug || false === is_string($taxonSlug)) {
            throw new \InvalidArgumentException('Taxon slug is required to render breadcrumb.');
        }

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBySlug($taxonSlug, $this->localeContext->getLocaleCode());

        return $taxon;
    }
}
