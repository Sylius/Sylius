<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Form\Type\Writer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Csv writer type
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvWriterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delimiter', 'text', array(
                'label'       => 'sylius.form.writer.csv.delimiter',
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                    new Length(array('groups' => array('sylius'), 'min' => 1, 'max' => 1)),
                ),
            ))
            ->add('enclosure', 'text', array(
                'label'      => 'sylius.form.writer.csv.enclosure',
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                    new Length(array('groups' => array('sylius'), 'min' => 1, 'max' => 1)),
                ),
            ))
            ->add('file', 'text', array(
                'label'    => 'sylius.form.writer.file',
                'required' => true,
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
        return 'sylius_csv_writer';
    }
}