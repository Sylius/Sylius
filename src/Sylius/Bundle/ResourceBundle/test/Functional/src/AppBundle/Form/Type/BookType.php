<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class BookType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'sylius_translations', [
                'label' => 'title',
                'type' => 'app_book_translation',
            ])
            ->add('author', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_book';
    }
}
