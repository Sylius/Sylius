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

use Sylius\Bundle\CoreBundle\Form\Type\Taxon\TaxonImageType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType as BaseTaxonType;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class TaxonType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var TaxonInterface|null $taxon */
        $taxon = $builder->getData();
        /** @var array<string> $excludedTaxonCodes */
        $excludedTaxonCodes = [];

        if (null !== $taxon) {
            $taxonCode = $taxon->getCode();
            if (null !== $taxonCode) {
                $excludedTaxonCodes[] = $taxonCode;
            }

            $visitedTaxonIds = [spl_object_id($taxon) => true];
            foreach ($taxon->getChildren() as $child) {
                $this->addDescendantCodes($child, $excludedTaxonCodes, $visitedTaxonIds);
            }
        }

        $builder
            ->add('images', LiveCollectionType::class, [
                'entry_type' => TaxonImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_options' => [
                    'label' => 'sylius.ui.add_image',
                ],
                'button_delete_options' => [
                    'label' => 'sylius.ui.delete',
                ],
            ])
            ->add('parent', TaxonAutocompleteType::class, [
                'label' => 'sylius.form.taxon.parent',
                'multiple' => false,
                'required' => false,
                'extra_options' => [
                    'excluded_taxon_codes' => $excludedTaxonCodes,
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_taxon';
    }

    public function getParent(): string
    {
        return BaseTaxonType::class;
    }

    /**
     * @param array<string> $excludedTaxonCodes
     * @param array<int, true> $visitedTaxonIds
     *
     * @param-out array<string> $excludedTaxonCodes
     */
    private function addDescendantCodes(TaxonInterface $taxon, array &$excludedTaxonCodes, array &$visitedTaxonIds): void
    {
        $taxonId = spl_object_id($taxon);
        if (isset($visitedTaxonIds[$taxonId])) {
            return;
        }

        $visitedTaxonIds[$taxonId] = true;

        $taxonCode = $taxon->getCode();
        if (null !== $taxonCode) {
            $excludedTaxonCodes[] = $taxonCode;
        }

        foreach ($taxon->getChildren() as $child) {
            $this->addDescendantCodes($child, $excludedTaxonCodes, $visitedTaxonIds);
        }
    }
}
