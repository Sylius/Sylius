<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Form\Type;

use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class TaxonChoiceType extends AbstractType
{
    /**
     * @var TaxonRepositoryInterface
     */
    protected $taxonRepository;

    /**
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(TaxonRepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ChoiceView $choice */
        foreach ($view->vars['choices'] as $choice) {
            $choice->label = str_repeat('— ', $choice->data->getLevel()).$choice->label;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildTreeChoices($choices, $level = 0)
    {
        $result = [];

        /** @var TaxonInterface $choice */
        foreach ($choices as $choice) {
            $result[] = new ChoiceView(
                str_repeat('-', $level).' '.$choice->getName(),
                $choice->getId(),
                $choice,
                []
            );

            if (!$choice->getChildren()->isEmpty()) {
                $result = array_merge(
                    $result,
                    $this->buildTreeChoices($choice->getChildren(), $level + 1)
                );
            }
        }

        return $result;
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $repository = $this->taxonRepository;
        $choiceList = function (Options $options) use ($repository) {
            /* @var TaxonRepositoryInterface $repository */
            if (null !== $options['root']) {
                if (is_string($options['root'])) {
                    $taxons = $repository->findChildrenByRootCode($options['root']);
                } else {
                    $taxons = $repository->findChildren($options['root']);
                }
            } else {
                $taxons = $repository->findNodesTreeSorted();
            }

            if (null !== $options['filter']) {
                $taxons = array_filter($taxons, $options['filter']);
            }

            return new ObjectChoiceList($taxons, null, [], null, 'id');
        };

        $resolver
            ->setDefaults([
                'choice_translation_domain' => false,
                'choice_list' => $choiceList,
                'root' => null,
                'filter' => null,
            ])
            ->setAllowedTypes('root', [TaxonInterface::class, 'string', 'null'])
            ->setAllowedTypes('filter', ['callable', 'null'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_taxon_choice';
    }
}
