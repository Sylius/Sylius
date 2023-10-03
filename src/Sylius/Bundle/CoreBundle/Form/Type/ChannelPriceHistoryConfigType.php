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

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Webmozart\Assert\Assert;

final class ChannelPriceHistoryConfigType extends AbstractResourceType implements DataMapperInterface
{
    public function __construct(
        private DataMapperInterface $propertyPathDataMapper,
        string $dataClass,
        array $validationGroups = [],
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lowestPriceForDiscountedProductsVisible', CheckboxType::class, [
                'label' => 'sylius.form.admin.channel.lowest_price_for_discounted_products_visible',
                'required' => false,
            ])
            ->add('lowestPriceForDiscountedProductsCheckingPeriod', IntegerType::class, [
                'label' => 'sylius.form.admin.channel.period_for_which_the_lowest_price_is_calculated',
            ])
            ->add('taxonsExcludedFromShowingLowestPrice', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.taxons_for_which_the_lowest_price_is_not_displayed',
                'required' => false,
                'multiple' => true,
            ])
        ;

        $builder->setDataMapper($this);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $this->propertyPathDataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        Assert::isInstanceOf($channelPriceHistoryConfig = $viewData, ChannelPriceHistoryConfigInterface::class);

        /** @var \Traversable $traversableForms */
        $traversableForms = $forms;
        $forms = iterator_to_array($traversableForms);

        $channelPriceHistoryConfig->clearTaxonsExcludedFromShowingLowestPrice();

        /** @var Collection $excludedTaxons */
        $excludedTaxons = $forms['taxonsExcludedFromShowingLowestPrice']->getData();

        /** @var TaxonInterface $taxon */
        foreach ($excludedTaxons as $taxon) {
            $channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($taxon);
        }

        unset($forms['taxonsExcludedFromShowingLowestPrice']);

        $this->propertyPathDataMapper->mapFormsToData(new ArrayCollection($forms), $viewData);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_channel_price_history_config';
    }
}
