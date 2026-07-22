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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler\Shop\Account;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\Shop\Account\SendShopUserVerificationEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\Shop\Account\SendShopUserVerificationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[CoversClass(SendShopUserVerificationEmailHandler::class)]
final class SendShopUserVerificationEmailHandlerTest extends TestCase
{
    /** @var MockObject&UserRepositoryInterface<ShopUserInterface> */
    private MockObject&UserRepositoryInterface $shopUserRepository;

    /** @var ChannelRepositoryInterface<ChannelInterface>&MockObject */
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private AccountVerificationEmailManagerInterface&MockObject $accountVerificationEmailManager;

    private SendShopUserVerificationEmailHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->accountVerificationEmailManager = $this->createMock(AccountVerificationEmailManagerInterface::class);
        $this->handler = new SendShopUserVerificationEmailHandler(
            $this->shopUserRepository,
            $this->channelRepository,
            $this->accountVerificationEmailManager,
        );
    }

    public function testIsAMessageHandler(): void
    {
        $attributes = (new \ReflectionClass($this->handler::class))->getAttributes(AsMessageHandler::class);

        self::assertCount(1, $attributes);
    }

    public function testSendsAccountVerificationEmail(): void
    {
        $shopUser = $this->createMock(ShopUserInterface::class);
        $channel = $this->createChannel(['en_US']);

        $this->shopUserRepository->method('findOneByEmail')->with('shop@example.com')->willReturn($shopUser);
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn($channel);
        $this->accountVerificationEmailManager
            ->expects(self::once())
            ->method('sendAccountVerificationEmail')
            ->with($shopUser, $channel, 'en_US')
        ;

        ($this->handler)(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testThrowsAnExceptionWhenShopUserHasNotBeenFound(): void
    {
        $this->shopUserRepository->method('findOneByEmail')->with('shop@example.com')->willReturn(null);
        $this->accountVerificationEmailManager->expects(self::never())->method('sendAccountVerificationEmail');

        self::expectException(\InvalidArgumentException::class);

        ($this->handler)(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testThrowsAnExceptionWhenChannelHasNotBeenFound(): void
    {
        $this->shopUserRepository->method('findOneByEmail')->with('shop@example.com')->willReturn($this->createMock(ShopUserInterface::class));
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn(null);
        $this->accountVerificationEmailManager->expects(self::never())->method('sendAccountVerificationEmail');

        self::expectException(\InvalidArgumentException::class);

        ($this->handler)(new SendShopUserVerificationEmail('shop@example.com', 'en_US', 'WEB'));
    }

    public function testThrowsAnExceptionWhenLocaleIsNotAvailableInChannel(): void
    {
        $this->shopUserRepository->method('findOneByEmail')->with('shop@example.com')->willReturn($this->createMock(ShopUserInterface::class));
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn($this->createChannel(['en_US']));
        $this->accountVerificationEmailManager->expects(self::never())->method('sendAccountVerificationEmail');

        self::expectException(\InvalidArgumentException::class);

        ($this->handler)(new SendShopUserVerificationEmail('shop@example.com', 'fr_FR', 'WEB'));
    }

    /** @param string[] $localeCodes */
    private function createChannel(array $localeCodes): ChannelInterface&MockObject
    {
        $locales = [];
        foreach ($localeCodes as $localeCode) {
            $locale = $this->createMock(LocaleInterface::class);
            $locale->method('getCode')->willReturn($localeCode);
            $locales[] = $locale;
        }

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getLocales')->willReturn(new ArrayCollection($locales));

        return $channel;
    }
}
