<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RBAC Permission choice form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionEntityType extends ResourceChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'query_builder' => function (EntityRepository $repository) {
                return $repository->createQueryBuilder('o')->orderBy('o.left', 'asc');
            },
        ]);
    }
}
