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

namespace Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Exception\VariantWithNoOptionsValuesException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class GenerateProductVariantsSubscriber implements EventSubscriberInterface
{
    public function __construct(private ProductVariantGeneratorInterface $generator, private /* Session */ $requestStack)
    {
        /** @phpstan-ignore-next-line */
        if (!$requestStack instanceof SessionInterface && !$requestStack instanceof RequestStack) {
            throw new \InvalidArgumentException(sprintf('The second argument of "%s" should be instance of "%s" or "%s"', __METHOD__, SessionInterface::class, RequestStack::class));
        }

        if ($requestStack instanceof SessionInterface) {
            @trigger_error(sprintf('Passing an instance of %s as constructor argument for %s is deprecated as of Sylius 1.12 and will be removed in 2.0. Pass an instance of %s instead.', SessionInterface::class, self::class, RequestStack::class), \E_USER_DEPRECATED);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $product = $event->getData();

        /** @var ProductInterface $product */
        Assert::isInstanceOf($product, ProductInterface::class);

        try {
            $this->generator->generate($product);
        } catch (VariantWithNoOptionsValuesException $exception) {
            if ($this->requestStack instanceof SessionInterface) {
                $session = $this->requestStack;
            } else {
                $session = $this->requestStack->getSession();
            }

            $session->getFlashBag()->add('error', $exception->getMessage());
        }
    }
}
