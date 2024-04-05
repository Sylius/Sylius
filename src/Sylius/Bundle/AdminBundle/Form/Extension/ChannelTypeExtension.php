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

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('menuTaxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.channel.menu_taxon',
                'multiple' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        yield ChannelType::class;
    }
}
