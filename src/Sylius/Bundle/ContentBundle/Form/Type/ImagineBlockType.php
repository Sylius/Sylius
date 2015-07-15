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

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Imagine block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ImagineBlockType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $dataClass = null;

    /**
     * @var string[]
     */
    protected $validationGroups = array();

    /**
     * @var FilterConfiguration
     */
    protected $filterConfiguration;

    /**
     * ImagineBlockType constructor.
     *
     * @param string $dataClass
     * @param array $validationGroups
     * @param FilterConfiguration $filterConfiguration
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        FilterConfiguration $filterConfiguration
    ) {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
        $this->filterConfiguration = $filterConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $filters = array();

        foreach (array_keys($this->filterConfiguration->all()) as $filter) {
            $filters[$filter] = sprintf('sylius.form.imagine_block.filter.%s', $filter);
        }

        $builder
            ->add('publishable', null, array(
                'label' => 'sylius.form.imagine_block.publishable'
                ))
            ->add('publishStartDate', 'datetime', array(
                'label' => 'sylius.form.imagine_block.publish_start_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('publishEndDate', 'datetime', array(
                'label' => 'sylius.form.imagine_block.publish_end_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('parentDocument', null, array(
                'label' => 'sylius.form.imagine_block.parent'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.imagine_block.internal_name'
            ))
            ->add('label', 'text', array(
                'label' => 'sylius.form.imagine_block.label',
                'required' => false
            ))
            ->add('linkUrl', 'text', array(
                'label' => 'sylius.form.imagine_block.link_url',
                'required' => false
            ))
            ->add('filter', 'choice', array(
                'choices' => $filters,
                'label' => 'sylius.form.imagine_block.filter',
                'required' => false,
            ))
            ->add('image', 'cmf_media_image', array(
                'label' => 'sylius.form.imagine_block.image',
                'attr' => array('class' => 'imagine-thumbnail'),
                'required' => false
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_imagine_block';
    }
}
