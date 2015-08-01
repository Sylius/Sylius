<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Form\Type\Reader;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvReaderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delimiter', 'text', array(
                'label'    => 'sylius.form.reader.csv.delimiter',
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                    new Length(array('groups' => array('sylius'), 'min' => 1, 'max' => 1)),
                ),
            ))
            ->add('enclosure', 'text', array(
                'label'    => 'sylius.form.reader.csv.enclosure',
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                    new Length(array('groups' => array('sylius'), 'min' => 1, 'max' => 1)),
                ),
            ))
            ->add('batch', 'integer', array(
                'label'    => 'sylius.form.reader.batch_size',
                'empty_data'     => '100',
            ))
            ->add('header', 'checkbox', array(
                'label'    => 'sylius.form.reader.csv.header',
                'required' => false,
            ))
            ->add('file', 'text', array(
                'label'    => 'sylius.form.writer.file',
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_csv_reader';
    }
}
