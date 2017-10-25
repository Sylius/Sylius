<?php
/**
 *
 *
 * @author Asier Marqués <asiermarques@gmail.com>
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type;


use Doctrine\ORM\EntityManager;
use Sylius\Component\Attribute\Repository\AttributeSelectOptionRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Asier Marqués <asier@simettric.com>
 */
abstract class AttributeValueSelectOptionType extends AbstractType
{

    private $optionRepository;
    private $model_class;
    private $em;

    function __construct(AttributeSelectOptionRepositoryInterface $attributeSelectOptionRepository, EntityManager $manager )
    {
        $this->optionRepository = $attributeSelectOptionRepository;
        $this->model_class      = $attributeSelectOptionRepository->getClassName();
        $this->em = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $repository = $this->optionRepository;
        $em = $this->em;

        $resolver
            ->setDefault('class', $this->model_class)
            ->setRequired('attribute')
            ->setNormalizer('query_builder', function (Options $options) use ($repository, $em)
            {
                return $repository->getAttributeSelectOptionsQB($options["attribute"]);
            })
            ->setNormalizer('multiple', function (Options $options)
            {
                $config = $options["attribute"]->getConfiguration();
                return $config["multiple"];
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_attribute_value_select_option';
    }

}