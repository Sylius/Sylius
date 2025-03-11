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
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class TaxonMenuComponent
{
    use HookableComponentTrait;

    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        protected TaxonRepositoryInterface $taxonRepository,
        protected ChannelContextInterface $channelContext,
        protected LocaleContextInterface $localeContext,
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
            ->findChildrenByChannelMenuTaxon($menuTaxon, $this->localeContext->getLocaleCode())
        ;
    }
}
