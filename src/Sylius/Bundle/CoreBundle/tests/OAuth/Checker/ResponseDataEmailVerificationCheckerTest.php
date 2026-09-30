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

namespace Tests\Sylius\Bundle\CoreBundle\OAuth\Checker;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\SensioConnectUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\OAuth\Checker\EmailVerificationCheckerInterface;
use Sylius\Bundle\CoreBundle\OAuth\Checker\ResponseDataEmailVerificationChecker;

final class ResponseDataEmailVerificationCheckerTest extends TestCase
{
    private ResponseDataEmailVerificationChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new ResponseDataEmailVerificationChecker();
    }

    public function testImplementsEmailVerificationCheckerInterface(): void
    {
        $this->assertInstanceOf(EmailVerificationCheckerInterface::class, $this->checker);
    }

    /** @param array<array-key, mixed> $data */
    #[DataProvider('claimProvider')]
    public function testDeterminesVerificationBasedOnTheClaimReturnedByTheProvider(array $data, bool $expected): void
    {
        $response = $this->createMock(UserResponseInterface::class);
        $response->method('getData')->willReturn($data);

        $this->assertSame($expected, $this->checker->isEmailVerified($response));
    }

    /** @return iterable<string, array{array<array-key, mixed>, bool}> */
    public static function claimProvider(): iterable
    {
        yield 'boolean true' => [['email_verified' => true], true];
        // Google documents the OpenID Connect claim as a string, hence a plain boolean cast would not be enough
        yield 'string "true", as documented for Google OpenID Connect' => [['email_verified' => 'true'], true];
        yield 'string "false", as documented for Google OpenID Connect' => [['email_verified' => 'false'], false];
        yield 'integer one' => [['email_verified' => 1], true];
        yield 'string "1"' => [['email_verified' => '1'], true];
        yield 'boolean false' => [['email_verified' => false], false];
        yield 'integer zero' => [['email_verified' => 0], false];
        yield 'null' => [['email_verified' => null], false];
        yield 'unrecognised value' => [['email_verified' => 'maybe'], false];
        // the endpoint GoogleResourceOwner points at by default returns this name, as a boolean
        yield 'legacy "oauth2/v1/userinfo" claim name' => [['verified_email' => true], true];
        yield 'legacy "oauth2/v1/userinfo" claim name, negative' => [['verified_email' => false], false];
        // Neither of the two names may be dropped: the very same provider reports the verification under a different
        // one depending on the endpoint it is pointed at. Switching Google's "infos_url" to the OpenID Connect
        // endpoint swaps "verified_email" for the standard "email_verified", and "id" for "sub" along with it.
        yield 'Google OpenID Connect "userinfo" payload' => [[
            'email' => 'shop@example.com',
            'email_verified' => true,
            'family_name' => 'Customer',
            'given_name' => 'Shop',
            'name' => 'Shop Customer',
            'sub' => '108412345678901234567',
        ], true];
        yield 'Google legacy "oauth2/v1/userinfo" payload' => [[
            'email' => 'shop@example.com',
            'family_name' => 'Customer',
            'given_name' => 'Shop',
            'id' => '108412345678901234567',
            'name' => 'Shop Customer',
            'verified_email' => true,
        ], true];
        // Facebook's default field set carries no verification signal whatsoever
        yield 'no claim at all' => [['email' => 'user@example.com'], false];
        yield 'empty response' => [[], false];
    }

    public function testPrefersTheStandardClaimOverTheLegacyOneWhenBothAreReturned(): void
    {
        $response = new PathUserResponse();
        $response->setData(['email_verified' => false, 'verified_email' => true]);

        $this->assertFalse($this->checker->isEmailVerified($response));
    }

    public function testPrefersTheClaimDeclaredByTheResourceOwner(): void
    {
        $response = new PathUserResponse();
        $response->setPaths(['email_verified' => 'attributes.mail_confirmed']);
        $response->setData(['attributes' => ['mail_confirmed' => true], 'email_verified' => false]);

        $this->assertTrue($this->checker->isEmailVerified($response));
    }

    public function testReturnsFalseWhenTheClaimDeclaredByTheResourceOwnerIsMissingFromTheResponse(): void
    {
        $response = new PathUserResponse();
        $response->setPaths(['email_verified' => 'attributes.mail_confirmed']);
        $response->setData(['email_verified' => true]);

        $this->assertFalse($this->checker->isEmailVerified($response));
    }

    public function testFallsBackToTheWellKnownClaimsWhenTheResourceOwnerDeclaresNoPath(): void
    {
        $response = new PathUserResponse();
        $response->setData(['email_verified' => true]);

        $this->assertTrue($this->checker->isEmailVerified($response));
    }

    /**
     * HWIOAuthBundle joins a path declared as a list into a single string - the way "realname" is assembled from a
     * first and a last name - which carries no meaning for a boolean claim, so such a declaration is not honoured.
     */
    public function testReturnsFalseWhenTheResourceOwnerDeclaresThePathAsAList(): void
    {
        $response = new PathUserResponse();
        $response->setPaths(['email_verified' => ['attributes.mail_confirmed', 'email_verified']]);
        $response->setData(['attributes' => ['mail_confirmed' => true], 'email_verified' => true]);

        $this->assertFalse($this->checker->isEmailVerified($response));
    }

    /**
     * PathUserResponse::getValueForPath() walks the steps without type-checking them, so the very same data makes it
     * raise a TypeError - the checker has to stay on the safe side of a response that is merely shaped unexpectedly.
     */
    public function testReturnsFalseWhenAStepOfTheDeclaredPathIsNotAnArray(): void
    {
        $response = new PathUserResponse();
        $response->setPaths(['email_verified' => 'attributes.mail_confirmed']);
        $response->setData(['attributes' => 'not-an-array']);

        $this->assertFalse($this->checker->isEmailVerified($response));
    }

    /**
     * ResponseInterface declares "@return array", but AbstractUserResponse::setData() stores whatever json_decode()
     * returned - a body of "true" leaves a boolean behind, and array_key_exists() would raise a TypeError on it.
     */
    public function testReturnsFalseWhenTheResponseDataIsNotAnArray(): void
    {
        $response = new PathUserResponse();
        $response->setData('true');

        $this->assertFalse($this->checker->isEmailVerified($response));
    }

    /**
     * SensioConnectUserResponse extends AbstractUserResponse rather than PathUserResponse and keeps a DOMNode in
     * place of the decoded payload, so the claim lookup must not assume an array even for a well-behaved provider.
     */
    public function testReturnsFalseForAResponseKeepingItsDataAsADomNode(): void
    {
        $response = new SensioConnectUserResponse();
        $response->setData(<<<XML
            <api xmlns:foaf="http://xmlns.com/foaf/0.1/">
                <root><foaf:Person><foaf:name>Shop Customer</foaf:name></foaf:Person></root>
            </api>
            XML);

        $this->assertNotInstanceOf(PathUserResponse::class, $response);
        $this->assertFalse($this->checker->isEmailVerified($response));
    }

    public function testLogsThatTheProviderReturnedNoVerificationClaimAtAll(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with(
            $this->stringContains('returns no e-mail verification claim at all'),
            $this->callback(
                fn (array $context): bool => 'facebook' === $context['resource_owner'] &&
                ResponseDataEmailVerificationChecker::CLAIMS === $context['claims'],
            ),
        );

        $checker = new ResponseDataEmailVerificationChecker(logger: $logger);

        $this->assertFalse($checker->isEmailVerified($this->createResponse(['email' => 'shop@example.com'], 'facebook')));
    }

    public function testLogsThatTheProviderDeniedTheVerification(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with(
            $this->stringContains('reports the e-mail address as unverified'),
            $this->callback(
                fn (array $context): bool => 'google' === $context['resource_owner'] &&
                'email_verified' === $context['claim'] &&
                false === $context['value'],
            ),
        );

        $checker = new ResponseDataEmailVerificationChecker(logger: $logger);

        $this->assertFalse($checker->isEmailVerified($this->createResponse(['email_verified' => false], 'google')));
    }

    public function testLogsAWarningWhenTheResponseDataIsNotAnArray(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');
        $logger->expects($this->once())->method('warning')->with(
            $this->stringContains('carries no array of data'),
            $this->callback(fn (array $context): bool => 'bool' === $context['data_type']),
        );

        $checker = new ResponseDataEmailVerificationChecker(logger: $logger);

        $this->assertFalse($checker->isEmailVerified($this->createResponse('true')));
    }

    public function testLogsNothingWhenTheProviderConfirmsTheAddress(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method($this->anything());

        $checker = new ResponseDataEmailVerificationChecker(logger: $logger);

        $this->assertTrue($checker->isEmailVerified($this->createResponse(['email_verified' => true])));
    }

    private function createResponse(mixed $data, string $resourceOwnerName = 'google'): PathUserResponse
    {
        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);
        $resourceOwner->method('getName')->willReturn($resourceOwnerName);

        $response = new PathUserResponse();
        $response->setResourceOwner($resourceOwner);
        $response->setData($data);

        return $response;
    }
}
