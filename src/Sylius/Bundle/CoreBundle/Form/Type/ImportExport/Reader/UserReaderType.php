<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\ImportExport\Reader;

use Sylius\Bundle\ImportExportBundle\Validator\Constraints\DateTimeFormat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReaderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('batch_size', 'number', array(
                'label'      => 'sylius.form.reader.batch_size',
                'required' => true,
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                ),
            ))
            ->add('date_format', 'text', array(
                'label'       => 'sylius.form.reader.user_orm.date_format',
                'data'        => 'Y-m-d H:i:s',
                'required'    => true,
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                    new DateTimeFormat(array('groups' => array('sylius'))),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user_orm_reader';
    }
}
