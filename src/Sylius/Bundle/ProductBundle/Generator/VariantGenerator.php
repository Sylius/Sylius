<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Generator;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Generator\VariantGenerator as BaseVariantGenerator;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Default product variants generator. It saves only valid variants.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantGenerator extends BaseVariantGenerator
{
    /**
     * Validator.
     *
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param RepositoryInterface      $variantRepository
     * @param ValidatorInterface       $validator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(RepositoryInterface $variantRepository, ValidatorInterface $validator, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($variantRepository);

        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function process(VariableInterface $variable, VariantInterface $variant)
    {
        if (0 < count($this->validator->validate($variant, array('sylius')))) {
            $variable->removeVariant($variant);
        } else {
            $this->eventDispatcher->dispatch('sylius.variant.pre_create', new GenericEvent($variant));
        }
    }
}
