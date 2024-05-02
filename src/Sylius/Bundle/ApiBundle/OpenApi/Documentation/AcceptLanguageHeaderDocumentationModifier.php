<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\OpenApi\Documentation;

use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AcceptLanguageHeaderDocumentationModifier implements DocumentationModifierInterface
{
    /**
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     */
    public function __construct(private RepositoryInterface $localeRepository)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $acceptLanguageHeaderParameter = new Parameter(
            name: 'Accept-Language',
            in: 'header',
            description: 'Locales in this enum are all locales defined in the shop and only enabled ones will work in the given channel in the shop.',
            required: false,
            schema: [
                'type' => 'string',
                'enum' => array_map(
                    fn (LocaleInterface $locale): string => $locale->getCode(),
                    $this->localeRepository->findAll(),
                ),
            ],
        );

        $pathItems = [];

        /** @var PathItem $pathItem */
        foreach ($docs->getPaths()->getPaths() as $path => $pathItem) {
            foreach (PathItem::$methods as $method) {
                $operation = $pathItem->{'get' . ucfirst($method)}();

                if (null === $operation) {
                    continue;
                }

                $parameters = $operation->getParameters();
                $parameters[] = $acceptLanguageHeaderParameter;

                $operation = $operation->withParameters($parameters);
                $pathItems[$path] = $pathItem->{'with' . ucfirst($method)}($operation);
            }
        }

        foreach ($pathItems as $path => $pathItem) {
            $docs->getPaths()->addPath($path, $pathItem);
        }

        return $docs;
    }
}
