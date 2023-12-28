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

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
final class TaxonAutocompleteChoiceType extends AbstractType
{
    public function __construct (
        private readonly string $entityClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->entityClass,
            'searchable_fields' => ['translations.name'],
            'choice_label' => 'fullname',
            'query_builder' => function (Options $options) {
                return function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('o');
                    $qb->andWhere('o.enabled = true');

                    $taxonsToBeExcluded = $options['extra_options']['excluded_taxons'] ?? [];
                    if ([] !== $taxonsToBeExcluded) {
                        $qb->andWhere($qb->expr()->notIn('o.id', $taxonsToBeExcluded));
                    }

                    return $qb;
                };
            }
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
