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

namespace Sylius\Behat\Context\Worker;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Webmozart\Assert\Assert;

/**
 * Context for the suite that walks the shop in worker mode.
 *
 * Three things set it apart from the other Behat contexts in Sylius, and
 * without any of them the suite measures nothing:
 *
 *  1. It talks to the application through a SOCKET, with its own HttpBrowser.
 *     Never through the kernel: a kernel booted inside the Behat process means
 *     there is no long-lived worker, and state carried between requests has
 *     nowhere to live.
 *
 *  2. Every "visitor" is a separate cookie jar, and therefore a separate
 *     session. Two tabs of one browser share a session and the second login
 *     overwrites the first - which looks exactly like a leak and is not one.
 *
 *  3. Channels are selected with the Host header, not with DNS, so the suite
 *     needs no /etc/hosts entry and no network aliases.
 *
 * The assertions are BEHAVIOURAL - they describe what a shopper sees. Reading
 * service state by reflection is a separate research instrument and does not
 * belong in this suite.
 */
final class WorkerModeContext implements Context
{
    private const PASSWORD = 'sylius';

    /** @var array<string, HttpBrowser> */
    private array $visitors = [];

    /** @var array<string, array<string, string>> */
    private array $identities = [];

    /** @var list<array{method: string, url: string, status: int}> */
    private array $log = [];

    private string $visitor = '1';

    private ?int $createdAdminId = null;

    private ?string $addedLocale = null;

    private ?string $apiJwtToken = null;

    private ?string $apiCartLocale = null;

    private ?string $apiCartCustomerEmail = null;

    private readonly string $channelAHost;

    private readonly string $channelBHost;

    private readonly string $baseUrl;

    private readonly string $channelAName;

    private readonly string $channelBName;

    /**
     * No constructor arguments: the context is not a container service, so
     * there is nothing to inject from. Addresses come from the environment so
     * that the CI job can point the suite elsewhere without touching config.
     */
    public function __construct()
    {
        $this->channelAHost = getenv('WORKER_CHANNEL_A') ?: 'http://channel-a.localhost:8080';
        $this->channelBHost = getenv('WORKER_CHANNEL_B') ?: 'http://channel-b.localhost:8080';
        $this->baseUrl = getenv('WORKER_BASE_URL') ?: 'http://localhost:8080';

        //  Channel names come from the environment rather than from constants,
        //  so the fixtures and the assertions cannot drift apart unnoticed.
        //  Channel A is Sylius' default fixture channel: it already carries the
        //  catalogue, where a freshly created channel would be empty.
        $this->channelAName = getenv('WORKER_CHANNEL_A_NAME') ?: 'Fashion Web Store';
        $this->channelBName = getenv('WORKER_CHANNEL_B_NAME') ?: 'Channel B';
    }

    /** @BeforeScenario */
    public function resetVisitors(): void
    {
        $this->visitors = [];
        $this->identities = [];
        $this->log = [];
        $this->visitor = '1';
        $this->createdAdminId = null;
        $this->addedLocale = null;
        $this->apiJwtToken = null;
        $this->apiCartLocale = null;
        $this->apiCartCustomerEmail = null;
    }

    // ----------------------------------------------------------- steps --

    #[When('I visit :path on channel :channel')]
    public function iVisitOnChannel(string $path, string $channel): void
    {
        $this->visit($channel, $path);
    }

    #[When('I browse :path alternating between both channels :count times')]
    public function browseAlternating(string $path, int $count): void
    {
        for ($i = 0; $i < $count; ++$i) {
            foreach (['A', 'B'] as $channel) {
                $this->visit($channel, $path);
            }
        }
    }

    #[When('I open the first product on channel :channel')]
    public function iOpenTheFirstProduct(string $channel): Crawler
    {
        $listing = $this->visit($channel, '/');
        $link = $listing->filter('a[href*="/products/"]')->first();

        Assert::greaterThan($link->count(), 0, sprintf('The homepage of channel %s has no product link.', $channel));

        return $this->request('GET', $link->link()->getUri());
    }

    #[When('I open the cart on channel :channel')]
    public function iOpenTheCart(string $channel): void
    {
        $this->visit($channel, '/' . $this->localeOf($channel) . '/cart/');
    }

    /**
     * Opening a product and the cart establishes a session with a cart attached
     * to it - the state that, in worker mode, may reach the next visitor.
     */
    #[When('I start a shopping session on channel :channel')]
    public function iStartAShoppingSession(string $channel): void
    {
        $this->iOpenTheFirstProduct($channel);
        $this->iOpenTheCart($channel);
    }

    /**
     * A new visitor is a NEW cookie jar. Reusing the old one would let the
     * second login overwrite the first, and the result would look like a leak
     * without being one.
     */
    #[When('a new visitor arrives')]
    public function aNewVisitorArrives(): void
    {
        $this->useVisitor('anon-' . bin2hex(random_bytes(3)));

        //  A new visitor carries NO credentials. Without clearing the token,
        //  later API requests would go out with the previous customer's bearer
        //  and the cart would rightly belong to them - the scenario would report
        //  a leak that is not there.
        $this->apiJwtToken = null;
        $this->apiCartLocale = null;
        $this->apiCartCustomerEmail = null;
    }

    #[When('I browse my account on channel :channel')]
    public function iBrowseMyAccount(string $channel): void
    {
        $locale = $this->localeOf($channel);

        foreach (['/account/dashboard', '/account/orders/', '/account/address-book/'] as $path) {
            $this->visit($channel, '/' . $locale . $path);
        }
    }

    /**
     * The behavioural form: instead of asking an instrument who the customer
     * context sees, check whether the account page renders THIS customer's
     * address. That is what a shopper sees, and all that can be checked without
     * reading service state.
     */
    #[Then('channel :channel sees the customer I registered')]
    public function channelSeesTheCustomerIRegistered(string $channel): void
    {
        $email = $this->identities[$this->visitor]['email']
            ?? throw new \RuntimeException(sprintf('Visitor %s never registered.', $this->visitor));

        $crawler = $this->visit($channel, '/' . $this->localeOf($channel) . '/account/dashboard');

        Assert::contains($crawler->html(), $email, sprintf(
            'Channel %s does not show the account of %s - registration should leave the customer logged in.',
            $channel,
            $email,
        ));
    }

    /**
     * The most direct symptom of a leaking channel context: the same URL renders
     * the name of the WRONG channel. Before the fix, the first host to reach a
     * freshly started process claimed the context for the whole process.
     */
    #[Then('channel :channel renders its own name')]
    public function channelRendersItsOwnName(string $channel): void
    {
        $crawler = $this->visit($channel, '/');
        $expected = $this->channelNameOf($channel);
        $other = $this->channelNameOf('A' === strtoupper($channel) ? 'B' : 'A');

        $html = $crawler->html();

        Assert::contains(
            $html,
            $expected,
            sprintf('Channel %s did not render its own name "%s".', $channel, $expected),
        );
        Assert::notContains(
            $html,
            $other,
            sprintf('Channel %s rendered the name of the other channel ("%s").', $channel, $other),
        );
    }

    #[When('I add the first product to the cart on channel :channel')]
    public function addFirstProductToCart(string $channel): void
    {
        $product = $this->iOpenTheFirstProduct($channel);
        $component = $this->liveComponent($product, 'sylius_shop:product:add_to_cart_form');

        //  The Live component only answers a request that looks like AJAX:
        //  without X-Requested-With the route does not match and returns 404.
        //  The payload has to carry every key a browser would send.
        $this->request(
            'POST',
            $this->hostOf($channel) . $component['url'] . '/addToCart',
            ['data' => json_encode(
                ['props' => $component['props'], 'updated' => [], 'args' => []],
                \JSON_THROW_ON_ERROR,
            )],
            [
                'Accept' => 'application/vnd.live-component+html',
                'X-Requested-With' => 'XMLHttpRequest',
            ],
        );
    }

    /**
     * A guard against a scenario that passes without checking anything: if any
     * request along the way returned 4xx or 5xx, later assertions could hold
     * only because the path was never walked.
     */
    #[Then('every request succeeded')]
    public function everyRequestSucceeded(): void
    {
        $failed = array_filter($this->log, static fn (array $e): bool => $e['status'] >= 400);

        Assert::isEmpty($failed, sprintf(
            "Requests that failed:\n%s",
            implode("\n", array_map(
                static fn (array $e): string => sprintf('  %d %s %s', $e['status'], $e['method'], $e['url']),
                $failed,
            )),
        ));
    }

    #[When('visitor :name registers on channel :channel')]
    public function visitorRegisters(string $name, string $channel): void
    {
        $this->useVisitor($name);

        $tag = bin2hex(random_bytes(3));
        $identity = [
            'firstName' => 'Imie' . $tag,
            'lastName' => 'Nazwisko' . $tag,
            'email' => sprintf('worker+%s@example.com', $tag),
            'phoneNumber' => (string) random_int(600000000, 699999999),
        ];
        $this->identities[$name] = $identity;

        $crawler = $this->visit($channel, '/' . $this->localeOf($channel) . '/register');
        $form = $crawler->filter('form')->first()->form();

        $values = [];
        foreach (array_keys($form->getValues()) as $field) {
            $lower = strtolower($field);
            $values[$field] = match (true) {
                str_contains($lower, 'email') => $identity['email'],
                str_contains($lower, 'firstname') => $identity['firstName'],
                str_contains($lower, 'lastname') => $identity['lastName'],
                str_contains($lower, 'phone') => $identity['phoneNumber'],
                str_contains($lower, 'password') => self::PASSWORD,
                default => $form->getValues()[$field],
            };
        }

        $this->submit($form, $values);
    }

    #[When('visitors :first and :second browse alternately on channel :channel :count times')]
    public function visitorsBrowseAlternately(string $first, string $second, string $channel, int $count): void
    {
        $locale = $this->localeOf($channel);

        for ($i = 0; $i < $count; ++$i) {
            foreach ([$first, $second] as $name) {
                $this->useVisitor($name);
                $this->visit($channel, '/');
                $this->visit($channel, '/' . $locale . '/account/dashboard');
            }
        }
    }

    /**
     * The profile form renders personal data straight into its field values.
     * It is the most direct screen on which a leaked identity would be visible
     * to a customer, which is why this checks the HTML rather than the security
     * context.
     */
    #[Then('visitor :name sees only their own personal data on channel :channel')]
    public function visitorSeesOnlyOwnPersonalData(string $name, string $channel): void
    {
        $this->useVisitor($name);

        $identity = $this->identities[$name]
            ?? throw new \RuntimeException(sprintf('Visitor %s never registered.', $name));

        $crawler = $this->visit($channel, '/' . $this->localeOf($channel) . '/account/profile/edit');

        Assert::same($this->statusOf($name), 200, sprintf(
            'Visitor %s got %d on /account/profile/edit - the session did not survive.',
            $name,
            $this->statusOf($name),
        ));

        $form = $crawler->filter('form[name="sylius_shop_customer_profile"]')->first();
        Assert::greaterThan($form->count(), 0, 'The profile page has no form.');

        $values = $form->form()->getValues();

        foreach ($identity as $field => $expected) {
            $key = 'sylius_shop_customer_profile[' . $field . ']';
            Assert::same($values[$key] ?? null, $expected, sprintf(
                'The profile form of visitor %s has %s = "%s", expected "%s".',
                $name,
                $field,
                $values[$key] ?? 'field missing',
                $expected,
            ));
        }

        $html = $crawler->html();

        foreach ($this->identities as $otherName => $other) {
            //  The cast is required: PHP turns the array keys "1" and "2" into
            //  integers, so a strict comparison with the visitor name would
            //  never match and every visitor would flag itself as foreign.
            if ((string) $otherName === $name) {
                continue;
            }

            foreach ($other as $field => $value) {
                Assert::notContains($html, $value, sprintf(
                    'The profile of visitor %s shows %s = "%s", which belongs to visitor %s.',
                    $name,
                    $field,
                    $value,
                    $otherName,
                ));
            }
        }
    }

    // ------------------------------------------- privileges and identity --

    #[When('visitor :name logs into the admin panel')]
    public function visitorLogsIntoTheAdminPanel(string $name): void
    {
        $this->useVisitor($name);
        $this->iLogIntoTheAdminPanel();
    }

    #[When('visitor :name visits :path on channel :channel')]
    public function visitorVisits(string $name, string $path, string $channel): void
    {
        $this->useVisitor($name);
        $this->visit($channel, $path);
    }

    /**
     * Two IDENTITIES interleaved, not just two sessions: an administrator and a
     * shopper hit the same worker in turn, so a back-office token has a chance
     * to reach a storefront request in the middle of someone else's session.
     */
    #[When('visitors :admin and :customer alternate admin and shop on channel :channel :count times')]
    public function visitorsAlternateAdminAndShop(string $admin, string $customer, string $channel, int $count): void
    {
        $locale = $this->localeOf($channel);

        for ($i = 0; $i < $count; ++$i) {
            $this->useVisitor($admin);
            $this->visit($channel, '/admin/');

            $this->useVisitor($customer);
            $this->visit($channel, '/');
            $this->visit($channel, '/' . $locale . '/account/dashboard');
        }
    }

    #[Then('visitor :name cannot reach the admin panel')]
    public function visitorCannotReachTheAdminPanel(string $name): void
    {
        $this->useVisitor($name);
        $crawler = $this->visit('A', '/admin/');

        Assert::greaterThan(
            $crawler->filter('form[action*="login"], input[name="_username"]')->count(),
            0,
            sprintf('Visitor %s was served the back office instead of the login screen.', $name),
        );
    }

    #[Then('visitor :name still has the admin panel')]
    public function visitorStillHasTheAdminPanel(string $name): void
    {
        $this->useVisitor($name);
        $crawler = $this->visit('A', '/admin/');

        Assert::same(
            $crawler->filter('input[name="_username"]')->count(),
            0,
            sprintf('Visitor %s was logged out of the back office.', $name),
        );
    }

    #[When('I log in as customer :email with password :password on channel :channel')]
    public function iLogInAsCustomer(string $email, string $password, string $channel): void
    {
        $crawler = $this->visit($channel, '/' . $this->localeOf($channel) . '/login');
        $form = $crawler->filter('form[action*="login-check"]')->first();

        Assert::greaterThan($form->count(), 0, 'The shop login page has no login-check form.');

        $this->submit($form->form(), ['_username' => $email, '_password' => $password]);
    }

    #[Then('channel :channel sees customer :email')]
    public function channelSeesCustomer(string $channel, string $email): void
    {
        $crawler = $this->visit($channel, '/' . $this->localeOf($channel) . '/account/dashboard');

        Assert::contains($crawler->html(), $email, sprintf(
            'Channel %s does not show the account of %s.',
            $channel,
            $email,
        ));
    }

    /**
     * The behavioural form again: not who the customer context sees, but whether
     * the account page opens at all. Sylius serves the login screen to anyone
     * not logged in, and that is all a visitor can observe.
     */
    #[Then('channel :channel sees no logged-in customer')]
    public function channelSeesNoLoggedInCustomer(string $channel): void
    {
        $crawler = $this->visit($channel, '/' . $this->localeOf($channel) . '/account/dashboard');

        Assert::greaterThan(
            $crawler->filter('input[name="_username"]')->count(),
            0,
            sprintf('Channel %s served the account panel to someone not logged in.', $channel),
        );
    }

    /**
     * The category link is looked up in the navigation first and, failing that,
     * on the product page, where the taxon shows up in the breadcrumbs. The menu
     * depends on the menu taxon assigned to the channel and a freshly created
     * channel may have none; this scenario is about listing pages, not about how
     * navigation is configured.
     */
    #[When('I open the first category on channel :channel')]
    public function iOpenTheFirstCategory(string $channel): void
    {
        $link = $this->visit($channel, '/')->filter('a[href*="/taxons/"]')->first();

        if (0 === $link->count()) {
            $link = $this->iOpenTheFirstProduct($channel)->filter('a[href*="/taxons/"]')->first();
        }

        if (0 === $link->count()) {
            $hrefs = $this->browser()->getCrawler()->filter('a[href]')->each(
                static fn (Crawler $n): string => (string) $n->attr('href'),
            );

            Assert::greaterThan($link->count(), 0, sprintf(
                "Channel %s has no category link, neither in the navigation nor on the product page.\nFirst links on the page: %s",
                $channel,
                implode(', ', array_slice($hrefs, 0, 12)),
            ));
        }

        $this->request('GET', $link->link()->getUri());
    }

    // ------------------------------------------------------------- API --

    #[When('I request the shop API :path on channel :channel')]
    public function iRequestTheShopApi(string $path, string $channel): void
    {
        $this->request('GET', $this->hostOf($channel) . $path, [], ['Accept' => 'application/ld+json']);
    }

    #[When('I authenticate via the shop API as :email with password :password on channel :channel')]
    public function iAuthenticateViaTheShopApi(string $email, string $password, string $channel): void
    {
        $this->requestRaw(
            'POST',
            $this->hostOf($channel) . '/api/v2/shop/customers/token',
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode(['email' => $email, 'password' => $password], \JSON_THROW_ON_ERROR),
        );

        Assert::same($this->currentStatus(), 200, sprintf('JWT login for %s failed.', $email));

        $data = json_decode($this->currentContent(), true, 512, \JSON_THROW_ON_ERROR);
        $token = $data['token'] ?? null;

        Assert::stringNotEmpty($token, 'The /api/v2/shop/customers/token response carries no JWT.');

        $this->apiJwtToken = $token;
    }

    #[When('I create a cart via the shop API on channel :channel')]
    public function iCreateACartViaTheShopApi(string $channel): void
    {
        $this->createApiCart($channel, null, expectCreated: true);
    }

    #[When('I create a cart via the shop API on channel :channel with Accept-Language :acceptLanguage')]
    public function iCreateACartViaTheShopApiWithAcceptLanguage(string $channel, string $acceptLanguage): void
    {
        $this->createApiCart($channel, $acceptLanguage, expectCreated: false);
    }

    #[When('I request my orders via the shop API on channel :channel')]
    public function iRequestMyOrdersViaTheShopApi(string $channel): void
    {
        $this->requestRaw('GET', $this->hostOf($channel) . '/api/v2/shop/orders', $this->apiHeaders());
    }

    /**
     * Regression for RequestHeaderBasedLocaleContext: the list of available
     * locales was remembered on first use, so Accept-Language negotiation on the
     * second channel happened against the locales of the first one.
     */
    #[Then('the API cart has locale :expected')]
    public function theApiCartHasLocale(string $expected): void
    {
        Assert::same($this->apiCartLocale, $expected, sprintf(
            'The API cart has locale "%s" instead of "%s" - the locale context is holding the list from the previous channel.',
            $this->apiCartLocale ?? 'null',
            $expected,
        ));
    }

    #[Then('the API cart belongs to customer :expectedEmail')]
    public function theApiCartBelongsToCustomer(string $expectedEmail): void
    {
        Assert::same($this->apiCartCustomerEmail, $expectedEmail, sprintf(
            'The API cart should belong to "%s" but belongs to "%s".',
            $expectedEmail,
            $this->apiCartCustomerEmail ?? 'nobody',
        ));
    }

    #[Then('the API cart belongs to no customer')]
    public function theApiCartBelongsToNoCustomer(): void
    {
        Assert::null($this->apiCartCustomerEmail, sprintf(
            'An anonymous API cart belongs to customer "%s".',
            $this->apiCartCustomerEmail ?? '',
        ));
    }

    // -------------------------------------------------------- checkout --

    /**
     * Walks the checkout with plain form submissions - address, shipping,
     * payment, confirmation - and then insists that an order actually came out
     * of it.
     *
     * The assertion is the point. A step whose page carries no form is skipped,
     * which is what lets the walk survive a shop configured to skip one; without
     * a check at the end, a checkout that silently went nowhere would still
     * report success and every later assertion would hold for the wrong reason.
     */
    #[When('I complete the checkout on channel :channel')]
    public function iCompleteTheCheckout(string $channel): void
    {
        $this->addFirstProductToCart($channel);

        $locale = $this->localeOf($channel);

        foreach (['address', 'select-shipping', 'select-payment', 'complete'] as $step) {
            $crawler = $this->visit($channel, sprintf('/%s/checkout/%s', $locale, $step));
            $form = $crawler->filter('form')->first();

            if (0 === $form->count()) {
                continue;
            }

            $this->submit($form->form(), $this->guessedValues($form->form()));
        }

        //  The browser's own URI, not the logged one: the log records the URL a
        //  request was sent to, and the order confirmation is reached through a
        //  redirect that follows the final submission.
        $landed = (string) $this->browser()->getHistory()->current()->getUri();

        Assert::true(
            str_contains($landed, 'thank-you') || str_contains($landed, '/order'),
            sprintf('The checkout did not end on an order. Landed on %s (HTTP %d).', $landed, $this->currentStatus()),
        );
    }

    // --------------------------------------------------- the back office --

    #[When('I log into the admin panel')]
    public function iLogIntoTheAdminPanel(): void
    {
        $crawler = $this->visit('A', '/admin/login');
        $form = $crawler->filter('form')->first();

        Assert::greaterThan($form->count(), 0, 'There is no form on /admin/login.');

        //  DomCrawler carries the hidden fields over, _csrf_admin_security_token
        //  among them.
        $this->submit($form->form(), [
            '_username' => getenv('ADMIN_EMAIL') ?: 'sylius@example.com',
            '_password' => getenv('ADMIN_PASSWORD') ?: 'sylius',
        ]);
    }

    #[When('I create a new administrator in the admin panel')]
    public function iCreateANewAdministrator(): void
    {
        $crawler = $this->visit('A', '/admin/users/new');
        $form = $crawler->filter('form[name="sylius_admin_admin_user"]')->first();

        Assert::greaterThan($form->count(), 0, 'There is no sylius_admin_admin_user form on /admin/users/new.');

        $suffix = bin2hex(random_bytes(4));
        $this->submit($form->form(), [
            'sylius_admin_admin_user[username]' => 'worker_admin_' . $suffix,
            'sylius_admin_admin_user[email]' => sprintf('worker_admin_%s@example.com', $suffix),
            'sylius_admin_admin_user[plainPassword]' => 'sylius123',
            'sylius_admin_admin_user[localeCode]' => 'en_US',
        ]);

        Assert::same($this->currentStatus(), 200, 'Creating the administrator failed.');

        $last = end($this->log);
        if (1 === preg_match('/[?&]id=(\d+)/', (string) ($last['url'] ?? ''), $m)) {
            $this->createdAdminId = (int) $m[1];
        }
    }

    /**
     * Regression for UserType: the validation_groups closure appended
     * 'sylius_user_create' straight onto a property of the shared service. After
     * the first administrator was created, every later EDIT on that worker
     * inherited the group and demanded a non-empty password.
     */
    #[Then('I can edit that administrator without entering a password')]
    public function iCanEditThatAdministratorWithoutAPassword(): void
    {
        $id = $this->createdAdminId ?? 1;
        $crawler = $this->visit('A', sprintf('/admin/users/%d/edit', $id));
        $form = $crawler->filter('form[name="sylius_admin_admin_user"]')->first();

        Assert::greaterThan($form->count(), 0, sprintf('There is no form on /admin/users/%d/edit.', $id));

        $result = $this->submit($form->form(), [
            'sylius_admin_admin_user[firstName]' => 'WorkerEdited',
            'sylius_admin_admin_user[plainPassword]' => '',
        ]);

        if (200 !== $this->currentStatus()) {
            $errors = $result->filter('.invalid-feedback')->each(static fn (Crawler $n): string => trim($n->text()));

            Assert::same($this->currentStatus(), 200, sprintf(
                'Editing an administrator without a password was rejected (HTTP %d, errors: %s) - UserType::$validationGroups kept the sylius_user_create group from an earlier request.',
                $this->currentStatus(),
                [] !== $errors ? implode('; ', $errors) : 'no details',
            ));
        }
    }

    #[When('I open the new taxon form in the admin panel')]
    public function iOpenTheNewTaxonForm(): void
    {
        $this->visit('A', '/admin/taxons/new');
    }

    /**
     * The locale is picked from the available choices rather than hardcoded.
     * The form offers only languages not yet defined in the shop, so a scenario
     * naming a fixed code passes once and fails on the second run against the
     * same database with an invalid value.
     */
    #[When('I add a new locale in the admin panel')]
    public function iAddANewLocale(): void
    {
        $crawler = $this->visit('A', '/admin/locales/new');
        $form = $crawler->filter('form[name="sylius_admin_locale"]')->first();

        Assert::greaterThan($form->count(), 0, 'There is no sylius_admin_locale form on /admin/locales/new.');

        $choices = $form->form()->get('sylius_admin_locale[code]')->availableOptionValues();
        $choices = array_values(array_filter($choices, static fn (string $v): bool => '' !== $v));

        Assert::notEmpty($choices, 'The locale form offers no value to choose from.');

        $this->addedLocale = $choices[0];
        $this->submit($form->form(), ['sylius_admin_locale[code]' => $this->addedLocale]);

        Assert::same($this->currentStatus(), 200, sprintf('Adding the locale %s failed.', $this->addedLocale));
    }

    /**
     * Regression for ResourceTranslationsType: the locale list was resolved in
     * the constructor and handed to OptionsResolver as a static array.
     * ResolvedFormType calls configureOptions once per worker, so a newly added
     * language never appeared in translation forms until the process restarted.
     */
    #[Then('the new taxon form includes a translation field for that locale')]
    public function theNewTaxonFormIncludesATranslationFieldForThatLocale(): void
    {
        $localeCode = $this->addedLocale
            ?? throw new \RuntimeException('No locale was added in this scenario.');

        $crawler = $this->visit('A', '/admin/taxons/new');
        $selector = sprintf('input[name="sylius_admin_taxon[translations][%s][name]"]', $localeCode);

        Assert::greaterThan($crawler->filter($selector)->count(), 0, sprintf(
            'The /admin/taxons/new form has no translation field for "%s" - ResourceTranslationsType froze the locale list on first use.',
            $localeCode,
        ));
    }

    // --------------------------------------------------------- internals --

    private function useVisitor(string $name): void
    {
        $this->visitors[$name] ??= new HttpBrowser(
            HttpClient::create(['max_redirects' => 0, 'timeout' => 30]),
            null,
            new CookieJar(),
        );

        $this->visitor = $name;
    }

    private function browser(): HttpBrowser
    {
        $this->useVisitor($this->visitor);

        return $this->visitors[$this->visitor];
    }

    private function visit(string $channel, string $path): Crawler
    {
        return $this->request('GET', $this->hostOf($channel) . $path);
    }

    /**
     * @param array<string, string> $parameters
     * @param array<string, string> $headers
     */
    private function request(string $method, string $url, array $parameters = [], array $headers = []): Crawler
    {
        $server = [];
        foreach ($headers as $header => $value) {
            $server['HTTP_' . strtoupper(str_replace('-', '_', $header))] = $value;
        }

        $crawler = $this->browser()->request($method, $url, $parameters, [], $server);
        $crawler = $this->followRedirects($crawler);

        $this->log[] = ['method' => $method, 'url' => $url, 'status' => $this->browser()->getResponse()->getStatusCode()];

        return $crawler;
    }

    /** @param array<string, string> $values */
    private function submit(\Symfony\Component\DomCrawler\Form $form, array $values = []): Crawler
    {
        $url = $form->getUri();
        $crawler = $this->followRedirects($this->browser()->submit($form, $values));

        $this->log[] = ['method' => $form->getMethod(), 'url' => $url, 'status' => $this->browser()->getResponse()->getStatusCode()];

        return $crawler;
    }

    /**
     * Redirects are followed by hand because every hop is a separate request
     * served by the worker - and therefore a separate chance to carry state.
     */
    private function followRedirects(Crawler $crawler, int $max = 5): Crawler
    {
        $browser = $this->browser();

        for ($i = 0; $i < $max; ++$i) {
            $status = $browser->getResponse()->getStatusCode();

            if ($status < 300 || $status >= 400) {
                break;
            }

            $crawler = $browser->followRedirect();
        }

        return $crawler;
    }

    /**
     * Creates a cart through the API and records the locale it was created in
     * and who it belongs to. The 201 is asserted only where the scenario expects
     * it: the Accept-Language variant is about language negotiation alone.
     */
    private function createApiCart(string $channel, ?string $acceptLanguage, bool $expectCreated): void
    {
        $headers = $this->apiHeaders();
        $headers['CONTENT_TYPE'] = 'application/ld+json';

        if (null !== $acceptLanguage) {
            $headers['HTTP_ACCEPT_LANGUAGE'] = $acceptLanguage;
        }

        $this->requestRaw('POST', $this->hostOf($channel) . '/api/v2/shop/orders', $headers, '{}');

        if ($expectCreated) {
            Assert::same($this->currentStatus(), 201, sprintf(
                'POST /api/v2/shop/orders returned %d - the cart context handed ApiCartBlamerListener an anonymous shop cart with a null tokenValue.',
                $this->currentStatus(),
            ));
        }

        $data = json_decode($this->currentContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->apiCartLocale = isset($data['localeCode']) ? (string) $data['localeCode'] : null;
        $this->apiCartCustomerEmail = isset($data['customer']['email']) ? (string) $data['customer']['email'] : null;
    }

    /** @return array<string, string> */
    private function apiHeaders(): array
    {
        $headers = ['HTTP_ACCEPT' => 'application/ld+json'];

        if (null !== $this->apiJwtToken) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->apiJwtToken;
        }

        return $headers;
    }

    /**
     * A request with a raw body and headers already in $_SERVER form: the API
     * needs CONTENT_TYPE and a JSON body, which form parameters cannot express.
     *
     * @param array<string, string> $server
     */
    private function requestRaw(string $method, string $url, array $server = [], ?string $content = null): Crawler
    {
        $crawler = $this->browser()->request($method, $url, [], [], $server, $content);

        $this->log[] = ['method' => $method, 'url' => $url, 'status' => $this->currentStatus()];

        return $crawler;
    }

    /**
     * Fills a form the way a shopper would have to: every choice takes its last
     * option, every empty text field a value guessed from its name.
     *
     * @return array<string, string>
     */
    private function guessedValues(\Symfony\Component\DomCrawler\Form $form): array
    {
        $values = [];

        foreach ($form->all() as $name => $field) {
            if ($field instanceof \Symfony\Component\DomCrawler\Field\ChoiceFormField) {
                $options = array_values(array_filter(
                    $field->availableOptionValues(),
                    static fn (string $v): bool => '' !== $v,
                ));

                if ([] === $options) {
                    continue;
                }

                //  The country decides whether any shipping method is offered at
                //  all: methods belong to zones, and a country outside every zone
                //  leaves the shipping step with nothing to choose, which Sylius
                //  answers with 422. Prefer the country the fixtures always put a
                //  zone and shipping methods in.
                $values[$name] = str_contains(strtolower($name), 'countrycode') && in_array('US', $options, true)
                    ? 'US'
                    : end($options);

                continue;
            }

            if (!$field instanceof \Symfony\Component\DomCrawler\Field\InputFormField || '' !== $field->getValue()) {
                continue;
            }

            //  Lower-cased on purpose: plainPassword would otherwise fall through
            //  to the default and the form would reject the submission.
            $lower = strtolower($name);

            $values[$name] = match (true) {
                str_contains($lower, 'email') => 'worker+checkout@example.com',
                str_contains($lower, 'password') => self::PASSWORD,
                str_contains($lower, 'postcode') => '00-001',
                str_contains($lower, 'phone') => '123456789',
                default => 'Worker',
            };
        }

        return $values;
    }

    private function currentContent(): string
    {
        return (string) $this->browser()->getResponse()->getContent();
    }

    private function currentStatus(): int
    {
        return $this->browser()->getResponse()->getStatusCode();
    }

    private function statusOf(string $name): int
    {
        $this->useVisitor($name);

        return $this->visitors[$name]->getResponse()->getStatusCode();
    }

    private function hostOf(string $channel): string
    {
        return match (strtoupper(trim($channel))) {
            'A' => $this->channelAHost,
            'B' => $this->channelBHost,
            default => $this->baseUrl,
        };
    }

    private function channelNameOf(string $channel): string
    {
        return 'A' === strtoupper(trim($channel)) ? $this->channelAName : $this->channelBName;
    }

    private function localeOf(string $channel): string
    {
        return 'A' === strtoupper(trim($channel)) ? 'en_US' : 'pl_PL';
    }

    /**
     * Adding to the cart in Sylius 2.x goes through a Symfony UX LiveComponent:
     * the form has no action, only data-live-* attributes. We read them and post
     * them back to /_components/<name>/addToCart, the way a browser would.
     *
     * @return array{url: string, props: array<string, mixed>}
     */
    private function liveComponent(Crawler $page, string $name): array
    {
        $node = $page->filter(sprintf('[data-live-name-value="%s"]', $name))->first();

        Assert::greaterThan($node->count(), 0, sprintf('LiveComponent "%s" not found.', $name));

        return [
            'url' => (string) $node->attr('data-live-url-value'),
            'props' => json_decode((string) $node->attr('data-live-props-value'), true, 512, \JSON_THROW_ON_ERROR),
        ];
    }
}
