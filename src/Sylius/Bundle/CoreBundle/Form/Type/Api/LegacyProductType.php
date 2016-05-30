<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Api;

use Sylius\Bundle\CoreBundle\Form\Type\LegacyProductType as BaseProductType;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Component\Form\FormBuilderInterface;

class LegacyProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('taxons', 'entity', [
                'multiple' => true,
                'class' => Taxon::class,
            ])
            ->remove('variantSelectionMethod')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'legacy_sylius_api_product';
    }
}
