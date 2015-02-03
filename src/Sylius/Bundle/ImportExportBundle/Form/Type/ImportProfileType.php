<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Form\Type;

use Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildReaderFormListener;
use Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildWriterFormListener;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Export profile form type.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImportProfileType extends AbstractResourceType
{
    /**
     * Reader registry
     *
     * @var ServiceRegistryInterface
     */
    protected $readerRegistry;

    /**
     * Writer registry
     *
     * @var ServiceRegistryInterface
     */
    protected $writerRegistry;

    /**
     * Constructor
     *
     * @param ServiceRegistryInterface $readerRegistry
     * @param ServiceRegistryInterface $writerRegistry
     */
    public function __construct($dataClass, array $validationGroups, ServiceRegistryInterface $readerRegistry, ServiceRegistryInterface $writerRegistry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->readerRegistry = $readerRegistry;
        $this->writerRegistry = $writerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildReaderFormListener($this->readerRegistry, $builder->getFormFactory()))
            ->addEventSubscriber(new BuildWriterFormListener($this->writerRegistry, $builder->getFormFactory()))
            ->add('name', 'text', array(
                'label'    => 'sylius.form.import_profile.name',
                'required' => true,
            ))
            ->add('code', 'text', array(
                'label'    => 'sylius.form.import_profile.code',
                'required' => true,
            ))
            ->add('description', 'textarea', array(
                'label'    => 'sylius.form.import_profile.description',
                'required' => false,
            ))
            ->add('reader', 'sylius_import_reader_choice', array(
                'label'    => 'sylius.form.reader.name',
                'required' => true,
            ))
            ->add('writer', 'sylius_import_writer_choice', array(
                'label'    => 'sylius.form.writer.name',
                'required' => true,
            ))
        ;

        $prototypes = array(
            'readers' => array(),
            'writers' => array(),
        );

        foreach ($this->readerRegistry->all() as $type => $reader) {
            $formType = sprintf('sylius_%s_reader', $reader->getType());

            if (!$formType) {
                continue;
            }

            try {
                $prototypes['readers'][$type] = $builder->create('readerConfiguration', $formType)->getForm();
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        foreach ($this->writerRegistry->all() as $type => $writer) {
            $formType = sprintf('sylius_%s_writer', $writer->getType());

            if (!$formType) {
                continue;
            }

            try {
                $prototypes['writers'][$type] = $builder->create('writerConfiguration', $formType)->getForm();
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }
        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = array();

        foreach ($form->getConfig()->getAttribute('prototypes') as $group => $prototypes) {
            foreach ($prototypes as $type => $prototype) {
                $view->vars['prototypes'][$group][$group.'_'.$type] = $prototype->createView($view);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_import_profile';
    }
}
