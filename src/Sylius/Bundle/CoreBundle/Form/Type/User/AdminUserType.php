<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\User;

use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Timezone;

final class AdminUserType extends UserType
{
    private ?string $fallbackLocale;

    public function __construct(string $dataClass, array $validationGroups = [], ?string $fallbackLocale = null)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->fallbackLocale = $fallbackLocale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('firstName', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.user.first_name',
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.user.last_name',
            ])
            ->add('localeCode', LocaleType::class, $this->provideLocaleCodeOptions())
            ->add('timezone', TimezoneType::class, [
                'label' => 'sylius.form.user.timezone',
                'required' => false,
            ])
            ->add('avatar', AvatarImageType::class, [
                'label' => 'sylius.ui.avatar',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_user';
    }

    private function provideLocaleCodeOptions(): array
    {
        $localeCodeOptions = [
            'label' => 'sylius.ui.locale',
            'placeholder' => null,
        ];

        if ($this->fallbackLocale !== null) {
            $localeCodeOptions['preferred_choices'] = [$this->fallbackLocale];
        }

        return $localeCodeOptions;
    }
}
