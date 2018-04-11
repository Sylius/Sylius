<?php
declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\PostCodeInterface;
use Sylius\Component\Addressing\Repository\PostCodeRepositoryInterface;
use Webmozart\Assert\Assert;

class PostCodeContext implements Context
{
    /** @var PostCodeRepositoryInterface */
    private $postcodeRepository;

    public function __construct(PostCodeRepositoryInterface $repository)
    {
        $this->postcodeRepository = $repository;
    }

    /**
     * @Transform /^postcode "([^"]+)"$/
     * @Transform /^"([^"]+)" postcode$/
     */
    public function getPostcodeByCode(string $code): PostCodeInterface
    {
        $postcode = $this->postcodeRepository->findOneBy(['postcode' => $code]);
        Assert::notNull($postcode, sprintf("Post code '%s' was not found by code", $code));

        return $postcode;
    }
}
