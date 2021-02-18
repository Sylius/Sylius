<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/** @experimental */
final class LocaleCodeAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    /** @var LocaleContextInterface */
    private $localeContext;

    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    public function transform($object, string $to, array $context = [])
    {
        if ($object->getLocaleCode() !== null) {
            return $object;
        }

        $localeCode = $this->localeContext->getLocaleCode();

        $object->setLocaleCode($localeCode);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof LocaleCodeAwareInterface;
    }
}
