<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule\Promotion;

use Sylius\Bundle\CoreBundle\Form\Type\Rule\TaxonomyConfigurationType as BaseTaxonomyConfigurationType;

/**
 * Taxonomy rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonomyConfigurationType extends BaseTaxonomyConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_taxonomy_configuration';
    }
}
