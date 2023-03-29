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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddBaseCurrencySubscriber;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\ChannelFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\Type\ShopBillingDataType;
use Sylius\Bundle\CoreBundle\Form\Type\TaxCalculationStrategyChoiceType;
use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Webmozart\Assert\Assert;

final class ChannelTypeExtension extends AbstractTypeExtension implements DataMapperInterface
{
    public function __construct(private DataMapperInterface $propertyPathDataMapper)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('locales', LocaleChoiceType::class, [
                'label' => 'sylius.form.channel.locales',
                'required' => false,
                'multiple' => true,
            ])
            ->add('defaultLocale', LocaleChoiceType::class, [
                'label' => 'sylius.form.channel.locale_default',
                'required' => true,
                'placeholder' => null,
            ])
            ->add('currencies', CurrencyChoiceType::class, [
                'label' => 'sylius.form.channel.currencies',
                'required' => false,
                'multiple' => true,
            ])
            ->add('countries', CountryChoiceType::class, [
                'label' => 'sylius.form.channel.countries',
                'required' => false,
                'multiple' => true,
            ])
            ->add('defaultTaxZone', ZoneChoiceType::class, [
                'required' => false,
                'label' => 'sylius.form.channel.tax_zone_default',
                'zone_scope' => Scope::TAX,
            ])
            ->add('taxCalculationStrategy', TaxCalculationStrategyChoiceType::class, [
                'label' => 'sylius.form.channel.tax_calculation_strategy',
            ])
            ->add('themeName', ThemeNameChoiceType::class, [
                'label' => 'sylius.form.channel.theme',
                'required' => false,
                'empty_data' => null,
                'placeholder' => 'sylius.ui.no_theme',
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'sylius.form.channel.contact_email',
                'required' => false,
            ])
            ->add('contactPhoneNumber', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.channel.contact_phone_number',
            ])
            ->add('skippingShippingStepAllowed', CheckboxType::class, [
                'label' => 'sylius.form.channel.skipping_shipping_step_allowed',
                'required' => false,
            ])
            ->add('skippingPaymentStepAllowed', CheckboxType::class, [
                'label' => 'sylius.form.channel.skipping_payment_step_allowed',
                'required' => false,
            ])
            ->add('accountVerificationRequired', CheckboxType::class, [
                'label' => 'sylius.form.channel.account_verification_required',
                'required' => false,
            ])
            ->add('shippingAddressInCheckoutRequired', CheckboxType::class, [
                'label' => 'sylius.form.channel.shipping_address_in_checkout_required',
                'required' => false,
            ])
            ->add('shopBillingData', ShopBillingDataType::class, [
                'label' => 'sylius.form.channel.shop_billing_data',
            ])
            ->add('menuTaxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.channel.menu_taxon',
            ])
            ->add('lowestPriceForDiscountedProductsVisible', CheckboxType::class, [
                'label' => 'sylius.form.channel.lowest_price_for_discounted_products_visible',
                'required' => false,
            ])
            ->add('lowestPriceForDiscountedProductsCheckingPeriod', IntegerType::class, [
                'label' => 'sylius.form.channel.period_for_which_the_lowest_price_is_calculated',
            ])
            ->add('taxonsExcludedFromShowingLowestPrice', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.taxons_for_which_the_lowest_price_is_not_displayed',
                'required' => false,
                'multiple' => true,
            ])
            ->addEventSubscriber(new AddBaseCurrencySubscriber())
            ->addEventSubscriber(new ChannelFormSubscriber())
        ;

        $builder->setDataMapper($this);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        Assert::isInstanceOf($channel = $viewData, ChannelInterface::class);

        /** @var \Traversable $traversableForms */
        $traversableForms = $forms;
        $forms = iterator_to_array($traversableForms);

        $channel->clearTaxonsExcludedFromShowingLowestPrice();
        /** @var Collection $excludedTaxons */
        $excludedTaxons = $forms['taxonsExcludedFromShowingLowestPrice']->getData();

        /** @var TaxonInterface $taxon */
        foreach ($excludedTaxons as $taxon) {
            $channel->addTaxonExcludedFromShowingLowestPrice($taxon);
        }

        unset($forms['taxonsExcludedFromShowingLowestPrice']);

        $this->propertyPathDataMapper->mapFormsToData(new ArrayCollection($forms), $viewData);
    }

    public function getExtendedType(): string
    {
        return ChannelType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms)
    {
        $this->propertyPathDataMapper->mapDataToForms($viewData, $forms);
    }
}
