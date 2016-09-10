<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The form type used to filter the promotion subjects when applying an action to them
 *
 * @author Viorel Craescu <viorel.craescu@trisoft.ro>
 * @author Gabi Udrescu <gabriel.udr@gmail.com>
 */

class ActionFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('price_range', PriceRangeType::class);
    }
}
