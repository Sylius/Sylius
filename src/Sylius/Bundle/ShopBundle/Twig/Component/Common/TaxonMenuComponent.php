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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Common;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
readonly class TaxonMenuComponent
{
    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private ChannelContextInterface $channelContext,
        private TaxonRepositoryInterface $taxonRepository,
        private LocaleContextInterface $localeContext,
    ) {
    }

    /**
     * @return TaxonInterface[]
     */
    #[ExposeInTemplate('taxons')]
    public function taxons(): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $menuTaxon = $channel->getMenuTaxon();

        return $this->taxonRepository
            ->findChildrenByChannelMenuTaxon($menuTaxon, $this->localeContext->getLocaleCode());
    }
}
