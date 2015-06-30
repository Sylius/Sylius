<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Static content choice type for phpcr_document choice form types.
 *
 * @author Jachim Coudenys <jachimcoudenys@gmail.com>
 */
class StaticContentChoiceType extends ResourceChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            array(
                'property' => 'id',
            )
        );
    }

}
