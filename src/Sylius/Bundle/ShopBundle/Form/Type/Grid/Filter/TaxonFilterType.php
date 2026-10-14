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

namespace Sylius\Bundle\ShopBundle\Form\Type\Grid\Filter;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Lists the enabled children of the "parent" taxon (or of the channel menu taxon when no parent is given).
 *
 * @experimental
 */
final class TaxonFilterType extends AbstractType
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(
        private readonly TaxonRepositoryInterface $taxonRepository,
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'parent' => null,
                'label' => false,
                'multiple' => true,
                // Not expanded on purpose: live components re-submit the extracted form values, which contain
                // null values for unchecked checkboxes, and expanded choices cannot handle them (array_flip() warning).
                // The live filter template renders the choices as checkboxes anyway.
                'expanded' => false,
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'choices' => fn (Options $options): array => $this->getTaxons($options['parent']),
            ])
            ->setAllowedTypes('parent', ['null', 'string'])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shop_grid_filter_taxon';
    }

    /** @return TaxonInterface[] */
    private function getTaxons(?string $parentCode): array
    {
        $localeCode = $this->localeContext->getLocaleCode();

        if (null !== $parentCode) {
            $taxons = $this->taxonRepository->findChildren($parentCode, $localeCode);
        } else {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            $taxons = $this->taxonRepository->findChildrenByChannelMenuTaxon($channel->getMenuTaxon(), $localeCode);
        }

        return array_values(array_filter(
            $taxons,
            static fn (TaxonInterface $taxon): bool => $taxon->isEnabled(),
        ));
    }
}
