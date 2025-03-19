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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\AdminBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelPriceHistoryConfigType as BaseChannelPriceHistoryConfigType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\RecursiveTransformer;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ReversedTransformer;
use Webmozart\Assert\Assert;

final class ChannelPriceHistoryConfigType extends AbstractType implements DataMapperInterface
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(
        private readonly TaxonRepositoryInterface $taxonRepository,
        private readonly DataMapperInterface $dataMapper,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxonsExcludedFromShowingLowestPrice', TaxonAutocompleteType::class, [
                'label' => 'sylius.ui.taxons_for_which_the_lowest_price_is_not_displayed',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'choice_value' => 'code',
            ])
        ;

        $builder->get('taxonsExcludedFromShowingLowestPrice')->addModelTransformer(
            new RecursiveTransformer(
                new ReversedTransformer(
                    new ResourceToIdentifierTransformer(
                        $this->taxonRepository,
                        'code',
                    ),
                ),
            ),
        );

        $builder->setDataMapper($this);
    }

    public function getParent(): string
    {
        return BaseChannelPriceHistoryConfigType::class;
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $this->dataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        Assert::isInstanceOf($channelPriceHistoryConfig = $viewData, ChannelPriceHistoryConfigInterface::class);

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $channelPriceHistoryConfig->clearTaxonsExcludedFromShowingLowestPrice();

        $excludedTaxonsForm = $forms['taxonsExcludedFromShowingLowestPrice'];

        /** @var iterable<TaxonInterface> $excludedTaxons */
        $excludedTaxons = $excludedTaxonsForm->getNormData() ?? [];
        foreach ($excludedTaxons as $taxon) {
            $channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($taxon);
        }

        unset($forms['taxonsExcludedFromShowingLowestPrice']);

        $this->dataMapper->mapFormsToData(new ArrayCollection($forms), $viewData);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_channel_price_history_config';
    }
}
