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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PostalCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $postalCodeRepository;

    /**
     * @param RepositoryInterface $postalCodeRepository
     */
    public function __construct(RepositoryInterface $postalCodeRepository)
    {
        $this->postalCodeRepository = $postalCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'choices'                   => function (Options $options): iterable {
                    dump($options);
                    /** @var CountryInterface $options['country'] */
                    if (null === $options['country']) {
                        return $this->postalCodeRepository->findAll();
                    }

                    return $options['country']->getPostalCodes();
                },
                'choice_value'              => 'code',
                'choice_label'              => 'name',
                'choice_translation_domain' => false,
                'country'                   => null,
                'label'                     => 'sylius.form.address.postal_code',
                'placeholder'               => 'sylius.form.postal_code.select',
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