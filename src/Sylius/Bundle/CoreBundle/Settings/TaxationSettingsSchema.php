<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Sylius\Bundle\SettingsBundle\Transformer\ResourceToIdentifierTransformer;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Taxation settings schema.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxationSettingsSchema implements SchemaInterface
{
    /**
     * Zone repository.
     *
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(RepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setOptional([
                'default_tax_zone',
            ])
            ->setAllowedTypes([
                'default_tax_zone' => ['null', ZoneInterface::class],
            ])
            ->setTransformer('default_tax_zone', new ResourceToIdentifierTransformer($this->zoneRepository))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('default_tax_zone', 'sylius_zone_choice', [
                'required' => false,
                'label' => 'sylius.form.settings.taxation.default_tax_zone',
            ])
        ;
    }
}
