<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 01/02/18
 * Time: 11:06
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Form\Type;


use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\PostCodeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PostCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $postCodeRepository;

    /**
     * @param RepositoryInterface $postCodeRepository
     */
    public function __construct(RepositoryInterface $postCodeRepository)
    {
        $this->postCodeRepository = $postCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'choices' => function (Options $options): iterable {
                    /** @var CountryInterface $options ['country'] */
                    if (null === $options['country']) {
                        return $this->postCodeRepository->findAll();
                    }

                    return $options['country']->getPostCodes();
                },
                'choice_value' => function (?PostCodeInterface $postCode) {
                    if ($postCode === null) {
                        return '';
                    }
                    return $postCode->getCode();
                },
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'country' => null,
                'label' => 'sylius.form.address.postal_code',
                'placeholder' => 'sylius.form.postal_code.select',
            ]
        );
        $resolver->addAllowedTypes('country', ['null', CountryInterface::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_postal_code_choice';
    }
}
