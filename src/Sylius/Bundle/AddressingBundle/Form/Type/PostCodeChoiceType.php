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
    private $postcodeRepository;

    /**
     * @param RepositoryInterface $postcodeRepository
     */
    public function __construct(RepositoryInterface $postcodeRepository)
    {
        $this->postcodeRepository = $postcodeRepository;
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
                        return $this->postcodeRepository->findAll();
                    }

                    return $options['country']->getPostCodes();
                },
                'choice_value' => function (?PostCodeInterface $postcode) {
                    if ($postcode === null) {
                        return '';
                    }
                    return $postcode->getCode();
                },
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'country' => null,
                'label' => 'sylius.form.address.postcode',
                'placeholder' => 'sylius.form.postcode.select',
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
        return 'sylius_postcode_choice';
    }
}
