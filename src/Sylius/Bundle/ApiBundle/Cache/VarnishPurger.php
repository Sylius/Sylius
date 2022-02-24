<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Cache;

use ApiPlatform\Core\HttpCache\PurgerInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

final class VarnishPurger implements PurgerInterface
{
    public function __construct(
        private PurgerInterface $basePurger,
        private UrlMatcherInterface $urlMatcher
    ) {
    }

    public function purge(array $iris)
    {
        $processedIris = [];
        foreach ($iris as $iri) {
            if ($this->isAdminIriReflectedInShop($iri)) {
                $processedIris[] = $this->processAdminIriToShop($iri);
            }

            $processedIris[] = $iri;
        }

        $this->basePurger->purge($processedIris);
    }

    private function isAdminIriReflectedInShop(string $iri): bool
    {
        if (!str_contains($iri, 'admin')) {
            return false;
        }

        try {
            $this->urlMatcher->match(str_replace('admin/', 'shop/', $iri));
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    private function processAdminIriToShop(string $iri): string
    {
        return str_replace('admin', 'shop', $iri);
    }
}
