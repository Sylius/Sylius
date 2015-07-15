<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\DataFetcher;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Data fetcher choice type
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DataFetcherChoiceType extends AbstractType
{
    /**
     * DataFetchers
     *
     * @var array
     */
    protected $dataFetchers;

    /**
     * Constructor.
     *
     * @param array $dataFetchers
     */
    public function __construct($dataFetchers)
    {
        $this->dataFetchers = $dataFetchers;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'choices' => $this->dataFetchers,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_data_fetcher_choice';
    }
}
