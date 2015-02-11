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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Reader choice choice type.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImportReaderChoiceType extends AbstractType
{
    /**
     * Writers
     *
     * @var array
     */
    protected $readers;

    /**
     * Constructor
     *
     * @param array $readers
     */
    public function __construct(array $readers)
    {
        $this->readers = $readers;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'choices' => $this->readers,
            ))
        ;
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'sylius_import_reader_choice';
    }
}
