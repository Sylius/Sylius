<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 01/02/18
 * Time: 11:06
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Form\Type;


use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class PostalCodeCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $postalCodeRepository;

    /**
     * @param RepositoryInterface $provinceRepository
     */
    public function __construct(RepositoryInterface $provinceRepository)
    {
        $this->postalCodeRepository = $provinceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new ReversedTransformer(
                new ResourceToIdentifierTransformer($this->postalCodeRepository, 'code')
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return PostalCodeChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_postal_code_code_choice';
    }
}