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
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

/**
 * The form type used to filter the promotion subjects using a price range
 *
 * @author Viorel Craescu <viorel.craescu@trisoft.ro>
 * @author Gabi Udrescu <gabriel.udr@gmail.com>
 */
class PriceRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('min', MoneyType::class, [
            'constraints' => [
                new Type(['type' => 'numeric']),
            ]
        ]);
        $builder->add('max', MoneyType::class, [
            'constraints' => [
                new Type(['type' => 'numeric']),
            ]
        ]);
    }
}
