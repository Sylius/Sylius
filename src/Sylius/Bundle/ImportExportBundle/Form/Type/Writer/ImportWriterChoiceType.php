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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Writer choice choice type.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ImportWriterChoiceType extends AbstractType
{
    /**
     * Writers
     *
     * @var array
     */
    protected $writers;

    /** 
     * Constructor
     * 
     * @param array $writers
     */
    public function __construct(array $writers)
    {
        $this->writers = $writers;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'choices' => $this->writers
            ))
        ;
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'sylius_import_writer_choice';
    }
}
