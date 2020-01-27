# CHANGELOG FOR `1.5.X`

## v1.5.9 (2020-01-27)

#### CVE-2020-5218: Ability to switch channels via GET parameter enabled in production environments

*Please refer to [the original security advisory](https://github.com/Sylius/Sylius/security/advisories/GHSA-prg5-hg25-8grq) for the most updated information.*  

**Impact:**

This vulnerability gives the ability to switch channels via the `_channel_code` GET parameter in production environments. This was meant to be enabled only when `%kernel.debug%` is set to true. 

However, if no `sylius_channel.debug` is set explicitly in the configuration, the default value which is `%kernel.debug%` will be not resolved and cast to boolean, enabling this debug feature even if that parameter is set to false.

**Patches:**

Patch has been provided for Sylius 1.3.x and newer - **1.3.16, 1.4.12, 1.5.9, 1.6.5**. Versions older than 1.3 are not covered by our security support anymore.

**Workarounds:**

Unsupported versions could be patched by adding the following configuration to run in production:

```yaml
sylius_channel:
    debug: false
```

#### Details

- [#9050](https://github.com/Sylius/Sylius/issues/9050) Added LazyCustomerLoader for OrderType of SyliusAdminApiBundle ([@jdeveloper](https://github.com/jdeveloper), [@lchrusciel](https://github.com/lchrusciel))
- [#9844](https://github.com/Sylius/Sylius/issues/9844) Fix ShippingPercentageDiscountPromotionActionCommand.php ([@cosyz2010](https://github.com/cosyz2010), [@Zales0123](https://github.com/Zales0123))
- [#10863](https://github.com/Sylius/Sylius/issues/10863) [SyliusUserBundle] Improve output of Promote/DemoteUserCommand ([@markbeazley](https://github.com/markbeazley))
- [#10901](https://github.com/Sylius/Sylius/issues/10901) Fix missing colon ([@reyostallenberg](https://github.com/reyostallenberg))
- [#10909](https://github.com/Sylius/Sylius/issues/10909) [Taxation] [Shipping] Fixed issue with shipping zones available to select in tax rate form (and the other way) ([@plewandowski](https://github.com/plewandowski))
- [#10916](https://github.com/Sylius/Sylius/issues/10916) [Docs] Improve platform.sh documentation for deployment ([@Tomanhez](https://github.com/Tomanhez))
- [#10922](https://github.com/Sylius/Sylius/issues/10922) fix: api URI for getting single product detail ([@hsharghi](https://github.com/hsharghi))
- [#10923](https://github.com/Sylius/Sylius/issues/10923) [Maintenance] Update PR template with supported versions ([@lchrusciel](https://github.com/lchrusciel))
- [#10926](https://github.com/Sylius/Sylius/issues/10926) Add lint:container command to the build & fix errors reported by it ([@pamil](https://github.com/pamil))
- [#10935](https://github.com/Sylius/Sylius/issues/10935) [Docs] Platform.sh cookbook refinement ([@CoderMaggie](https://github.com/CoderMaggie))
- [#10938](https://github.com/Sylius/Sylius/issues/10938) [Payum][Paypal] Use full price instead of discounted one ([@Prometee](https://github.com/Prometee))
- [#10943](https://github.com/Sylius/Sylius/issues/10943) Yaml standards ([@sspooky13](https://github.com/sspooky13), [@pamil](https://github.com/pamil))
- [#10947](https://github.com/Sylius/Sylius/issues/10947) [Channel] Prevent from adding default tax zone of a channel in a different scope than tax or all ([@GSadee](https://github.com/GSadee))
- [#10961](https://github.com/Sylius/Sylius/issues/10961) [Maintenance] Remove shipping bundle from spec namespace config ([@lchrusciel](https://github.com/lchrusciel))
- [#10963](https://github.com/Sylius/Sylius/issues/10963) Fix phpspec also on 1.5 ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10964](https://github.com/Sylius/Sylius/issues/10964) [Behat] Disallow w3c in Behat Selenium session ([@Zales0123](https://github.com/Zales0123))
- [#10979](https://github.com/Sylius/Sylius/issues/10979) [Installation] Inform about BitBagCommerce/SyliusCmsPlugin after installing Sylius ([@AdamKasp](https://github.com/AdamKasp))
- [#10995](https://github.com/Sylius/Sylius/issues/10995) Move Taxation core service from TaxationBundle to CoreBundle ([@hmonglee](https://github.com/hmonglee))
- [#11005](https://github.com/Sylius/Sylius/issues/11005) SyliusGridBundle downgrade lock ([@Tomanhez](https://github.com/Tomanhez), [@lchrusciel](https://github.com/lchrusciel))
- [#11006](https://github.com/Sylius/Sylius/issues/11006) [API] Fixed OrderController save action issue in not html requests ([@pfazzi](https://github.com/pfazzi))
- [#11013](https://github.com/Sylius/Sylius/issues/11013) Fix typo in PromotionCouponFactoryInterface ([@pamil](https://github.com/pamil))
- [#11019](https://github.com/Sylius/Sylius/issues/11019) [Documentation] Add hint about disabling autowire when extending a controller  ([@adrianmarte](https://github.com/adrianmarte))
- [#11022](https://github.com/Sylius/Sylius/issues/11022) Clarify release process regarding PHP versions + update the table ([@pamil](https://github.com/pamil))
- [#11024](https://github.com/Sylius/Sylius/issues/11024) Replace unbound behat/mink dependency with tagged friends-of-behat/mink fork ([@pamil](https://github.com/pamil))

## v1.5.7, v1.5.8 (2019-12-03, 2019-12-05)

#### CVE-2019-16768: Internal exception message exposure in login action.

**Details:**

Exception messages from internal exceptions (like database exception) are wrapped by 
`\Symfony\Component\Security\Core\Exception\AuthenticationServiceException` and propagated through the system to UI. 
Therefore, some internal system information may leak and be visible to the customer.

A validation message with the exception details will be presented to the user when one will try to log into the shop.

**Solution:**

This release patches the reported vulnerability. The `src/Sylius/Bundle/UiBundle/Resources/views/Security/_login.html.twig` 
file from Sylius should be overridden and `{{ messages.error(last_error.message) }}` changed to `{{ messages.error(last_error.messageKey) }}`.

#### Details

- [#10835](https://github.com/Sylius/Sylius/issues/10835) Improve deprecation message for "Sylius\Bundle\CoreBundle\Application\Kernel" ([@pamil](https://github.com/pamil))
- [#10841](https://github.com/Sylius/Sylius/issues/10841) [Docs] Include link to ShopApi docs to REST API Reference ([@Zales0123](https://github.com/Zales0123))
- [#10846](https://github.com/Sylius/Sylius/issues/10846) [Order] Include order unit promotion adjustments and order item promotion adjustments in order promotion total ([@Tomanhez](https://github.com/Tomanhez))
- [#10849](https://github.com/Sylius/Sylius/issues/10849) Move ShopApi reference to main menu ([@Zales0123](https://github.com/Zales0123))
- [#10855](https://github.com/Sylius/Sylius/issues/10855) [Docs] Open external links in a new tab ([@Zales0123](https://github.com/Zales0123))
- [#10857](https://github.com/Sylius/Sylius/issues/10857) Change readme banner ([@kulczy](https://github.com/kulczy))
- [#10880](https://github.com/Sylius/Sylius/issues/10880) [Promotion] Improve coupon generation validation message ([@GSadee](https://github.com/GSadee))
- [#10881](https://github.com/Sylius/Sylius/issues/10881) Add docs banner ([@kulczy](https://github.com/kulczy))
- [#10891](https://github.com/Sylius/Sylius/issues/10891) Update release process docs for 1.2 ([@pamil](https://github.com/pamil))

## v1.5.6 (2019-11-11)

#### Details

- [#9931](https://github.com/Sylius/Sylius/issues/9931) [Payum] infinite loop on state machine exception fixed ([@tautelis](https://github.com/tautelis))
- [#10734](https://github.com/Sylius/Sylius/issues/10734) Added: TimestampableInterface to core TaxonInterface (fixes #10728) ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10748](https://github.com/Sylius/Sylius/issues/10748) Switch statement conditions ([@mikemix](https://github.com/mikemix))
- [#10750](https://github.com/Sylius/Sylius/issues/10750) Fix compound form errors ([@loic425](https://github.com/loic425))
- [#10752](https://github.com/Sylius/Sylius/issues/10752) Translate attribute type on attributes grid ([@loic425](https://github.com/loic425))
- [#10755](https://github.com/Sylius/Sylius/issues/10755) [Docs] Add tag that stripe is outdated and add SCA note ([@Tomanhez](https://github.com/Tomanhez), [@GSadee](https://github.com/GSadee))
- [#10761](https://github.com/Sylius/Sylius/issues/10761) Replace EntityManager#flush($entity) by EntityManager#flush() ([@twojtylak](https://github.com/twojtylak))
- [#10764](https://github.com/Sylius/Sylius/issues/10764) [Behat] Fix a typo on Paypal context ([@loic425](https://github.com/loic425))
- [#10769](https://github.com/Sylius/Sylius/issues/10769) Remove unsupported RBAC plugin from command and docs ([@GSadee](https://github.com/GSadee))
- [#10773](https://github.com/Sylius/Sylius/issues/10773) Update ad url ([@kulczy](https://github.com/kulczy))
- [#10776](https://github.com/Sylius/Sylius/issues/10776) [Behat] Remove final on product index and product variant index pages ([@loic425](https://github.com/loic425))
- [#10781](https://github.com/Sylius/Sylius/issues/10781) Allow no default tax zone in channel fixtures ([@pamil](https://github.com/pamil))
- [#10790](https://github.com/Sylius/Sylius/issues/10790) [ShippingMethod] Do not allow to specify shipping charge below 0 ([@Zales0123](https://github.com/Zales0123))
- [#10792](https://github.com/Sylius/Sylius/issues/10792) [Behat][Admin] Add scenarios for validating default locale for a channel ([@GSadee](https://github.com/GSadee))
- [#10793](https://github.com/Sylius/Sylius/issues/10793) [Admin][Channel] Validating default locale for a channel ([@GSadee](https://github.com/GSadee))
- [#10805](https://github.com/Sylius/Sylius/issues/10805) [Addressing] Make sure the CountryNameExtension::translateCountryIsoCode() always returns a string ([@vvasiloi](https://github.com/vvasiloi))
- [#10806](https://github.com/Sylius/Sylius/issues/10806) [Order] include order promotion adjustments in order promotion total ([@vvasiloi](https://github.com/vvasiloi))
- [#10819](https://github.com/Sylius/Sylius/issues/10819) Fixed: Typo/artifact ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10820](https://github.com/Sylius/Sylius/issues/10820) Rename shop user factory to help autowiring ([@loic425](https://github.com/loic425))
- [#10821](https://github.com/Sylius/Sylius/issues/10821) Specify PHP version for SymfonyInsights ([@pamil](https://github.com/pamil))
- [#10823](https://github.com/Sylius/Sylius/issues/10823) Remove unnecessary +x chmod on some files ([@pamil](https://github.com/pamil))
- [#10824](https://github.com/Sylius/Sylius/issues/10824) Use SessionInterface instead of Session in UserImpersonator ([@pamil](https://github.com/pamil))
- [#10825](https://github.com/Sylius/Sylius/issues/10825) Fixed: Typo at grid configuration example ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10826](https://github.com/Sylius/Sylius/issues/10826) Execute PHPUnit tests inside AdminApiBundle ([@pamil](https://github.com/pamil))
- [#10832](https://github.com/Sylius/Sylius/issues/10832) Do not merge promotion action configuration ([@pamil](https://github.com/pamil))

## v1.5.5 (2019-10-08)

#### Details

- [#10641](https://github.com/Sylius/Sylius/issues/10641) [Documentation] Fixtures customization guides - fixes ([@CoderMaggie](https://github.com/CoderMaggie), [@Zales0123](https://github.com/Zales0123))
- [#10644](https://github.com/Sylius/Sylius/issues/10644) [Documentation] Add tip about locked adjustments ([@j0r1s](https://github.com/j0r1s))
- [#10645](https://github.com/Sylius/Sylius/issues/10645) [Docs] Fix Blackfire Ad ([@Tomanhez](https://github.com/Tomanhez))
- [#10646](https://github.com/Sylius/Sylius/issues/10646) [Docs] Fix Ad ([@Tomanhez](https://github.com/Tomanhez))
- [#10649](https://github.com/Sylius/Sylius/issues/10649) Update online course ad ([@kulczy](https://github.com/kulczy))
- [#10652](https://github.com/Sylius/Sylius/issues/10652) Add Sylius 1.6 banner to the docs ([@kulczy](https://github.com/kulczy))
- [#10667](https://github.com/Sylius/Sylius/issues/10667) Improve GUS information notification ([@Zales0123](https://github.com/Zales0123))
- [#10680](https://github.com/Sylius/Sylius/issues/10680) Fix ChannelCollector related serialization issue in Symfony profiler ([@ostrolucky](https://github.com/ostrolucky))
- [#10701](https://github.com/Sylius/Sylius/issues/10701) [Maintenance] Update docs with v1.6 ([@lchrusciel](https://github.com/lchrusciel))
- [#10710](https://github.com/Sylius/Sylius/issues/10710) [Address book] Extensibility improvements ([@cyrosy](https://github.com/cyrosy))
- [#10713](https://github.com/Sylius/Sylius/issues/10713) [Behat] Improve dashboard page extensibility ([@loic425](https://github.com/loic425))
- [#10727](https://github.com/Sylius/Sylius/issues/10727) Fix channels label size and alignment ([@kulczy](https://github.com/kulczy))
- [#10732](https://github.com/Sylius/Sylius/issues/10732) Update course ad ([@kulczy](https://github.com/kulczy))
- [#10739](https://github.com/Sylius/Sylius/issues/10739) [Admin][Adressing] fixed province code validation regex ([@twojtylak](https://github.com/twojtylak))
- [#10742](https://github.com/Sylius/Sylius/issues/10742) Fix the build for 1.5 and 1.6 branches ([@pamil](https://github.com/pamil))

## v1.5.4 (2019-08-27)

#### Details

- [#10395](https://github.com/Sylius/Sylius/issues/10395) [Docs] How to add your custom fixtures? ([@Tomanhez](https://github.com/Tomanhez))
- [#10397](https://github.com/Sylius/Sylius/issues/10397) [Docs]How to add your custom fixture suites? ([@Tomanhez](https://github.com/Tomanhez))
- [#10512](https://github.com/Sylius/Sylius/issues/10512) [Admin] Improve breadcrumbs (especially for ProductVariants and PromotionCoupons) ([@CoderMaggie](https://github.com/CoderMaggie))
- [#10540](https://github.com/Sylius/Sylius/issues/10540) Skip oauth_user_factory_is_not_overridden test if HWIOAuthBundle is not installed ([@vvasiloi](https://github.com/vvasiloi))
- [#10553](https://github.com/Sylius/Sylius/issues/10553) Flags are not languages ([@vvasiloi](https://github.com/vvasiloi))
- [#10558](https://github.com/Sylius/Sylius/issues/10558) Allow translation of custom labels ([@Prometee](https://github.com/Prometee))
- [#10564](https://github.com/Sylius/Sylius/issues/10564) [Fixture] Improve order fixture ([@Zales0123](https://github.com/Zales0123))
- [#10571](https://github.com/Sylius/Sylius/issues/10571) Update custom-promotion-rule.rst ([@jmwill86](https://github.com/jmwill86))
- [#10579](https://github.com/Sylius/Sylius/issues/10579) Fix lazy choice tree will not automatically expanded ([@tom10271](https://github.com/tom10271))
- [#10583](https://github.com/Sylius/Sylius/issues/10583) Enable sorting of customer orders in admin panel ([@pamil](https://github.com/pamil))
- [#10589](https://github.com/Sylius/Sylius/issues/10589) [Documentation][Cookbook] How to integrate a Payment Gateway as a Plugin? ([@lchrusciel](https://github.com/lchrusciel))
- [#10598](https://github.com/Sylius/Sylius/issues/10598) Add course ad ([@kulczy](https://github.com/kulczy))
- [#10599](https://github.com/Sylius/Sylius/issues/10599) [Documentation] Delete additional lines to remove ShopBundle ([@wpje](https://github.com/wpje))
- [#10600](https://github.com/Sylius/Sylius/issues/10600) [Documentation][Minor] Removing redundant dots ([@lchrusciel](https://github.com/lchrusciel))
- [#10601](https://github.com/Sylius/Sylius/issues/10601) Change course CTA ([@kulczy](https://github.com/kulczy))
- [#10603](https://github.com/Sylius/Sylius/issues/10603) [Shop] Promotion integrity checker fix ([@lchrusciel](https://github.com/lchrusciel))
- [#10605](https://github.com/Sylius/Sylius/issues/10605) [Admin][Shipment] Not displaying shipments in cart state on the list ([@GSadee](https://github.com/GSadee))
- [#10608](https://github.com/Sylius/Sylius/issues/10608) [Docs] Fix incorrect documentation regarding payments ([@dimaip](https://github.com/dimaip))
- [#10609](https://github.com/Sylius/Sylius/issues/10609) [Documentation][Minor] Proper comment in xml file ([@lchrusciel](https://github.com/lchrusciel))
- [#10613](https://github.com/Sylius/Sylius/issues/10613) [PayumBundle] Use Payment amount in Payum gateways actions (, [@Zales0123](https://github.com/Zales0123))
- [#10618](https://github.com/Sylius/Sylius/issues/10618) [Fixtures] Allow no shipping and payments in fixtures ([@igormukhingmailcom](https://github.com/igormukhingmailcom), [@Zales0123](https://github.com/Zales0123))
- [#10624](https://github.com/Sylius/Sylius/issues/10624) Disable chrome autocomplete ([@kulczy](https://github.com/kulczy))
- [#10626](https://github.com/Sylius/Sylius/issues/10626) [Fixture] Do not skip payments and shipments manually ([@Zales0123](https://github.com/Zales0123))
- [#10629](https://github.com/Sylius/Sylius/issues/10629) [Docs] Add missing items to customization guide menu ([@Zales0123](https://github.com/Zales0123))
- [#10633](https://github.com/Sylius/Sylius/issues/10633) Add Blackfire ad ([@kulczy](https://github.com/kulczy))
- [#10634](https://github.com/Sylius/Sylius/issues/10634) Add Blackfire logo ([@kulczy](https://github.com/kulczy))

## v1.5.3 (2019-07-25)

#### Details

- [#10069](https://github.com/Sylius/Sylius/issues/10069) [ShopBundle][PayumBundle] FIX payum authorize route ([@JaisDK](https://github.com/JaisDK), [@pamil](https://github.com/pamil), [@lchrusciel](https://github.com/lchrusciel))
- [#10116](https://github.com/Sylius/Sylius/issues/10116) Allow nullable shop billing data ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10121](https://github.com/Sylius/Sylius/issues/10121) [GridBundle] Doc improvement ([@Roshyo](https://github.com/Roshyo))
- [#10149](https://github.com/Sylius/Sylius/issues/10149) Add index on order.cart + order.updated_at for faster expired cart removal selection ([@stefandoorn](https://github.com/stefandoorn))
- [#10161](https://github.com/Sylius/Sylius/issues/10161) Orders index API endpoint ([@JaisDK](https://github.com/JaisDK), [@Zales0123](https://github.com/Zales0123))
- [#10163](https://github.com/Sylius/Sylius/issues/10163) [BuildFix] Fix AbstractMigration use statement ([@Zales0123](https://github.com/Zales0123))
- [#10166](https://github.com/Sylius/Sylius/issues/10166) ShopBillingData fixtures ([@Zales0123](https://github.com/Zales0123))
- [#10199](https://github.com/Sylius/Sylius/issues/10199) Allowing options to be given with resource[0].id syntax ([@Roshyo](https://github.com/Roshyo))
- [#10202](https://github.com/Sylius/Sylius/issues/10202) Expanding the customer fixtures ([@mamazu](https://github.com/mamazu))
- [#10209](https://github.com/Sylius/Sylius/issues/10209) [Shop] Use first variant image on a cart page ([@castler](https://github.com/castler), [@Zales0123](https://github.com/Zales0123))
- [#10212](https://github.com/Sylius/Sylius/issues/10212) Update UPGRADE-1.3.md diff link ([@oallain](https://github.com/oallain))
- [#10233](https://github.com/Sylius/Sylius/issues/10233) Payment status at  order history page ([@AdamKasp](https://github.com/AdamKasp))
- [#10234](https://github.com/Sylius/Sylius/issues/10234) Orders shipment status ([@Tomanhez](https://github.com/Tomanhez))
- [#10240](https://github.com/Sylius/Sylius/issues/10240) #9965 Feature/local in sylius install ([@oallain](https://github.com/oallain))
- [#10249](https://github.com/Sylius/Sylius/issues/10249) Browsing shipments ([@AdamKasp](https://github.com/AdamKasp))
- [#10250](https://github.com/Sylius/Sylius/issues/10250) See Manage coupons from template edit promotion  ([@Tomanhez](https://github.com/Tomanhez))
- [#10258](https://github.com/Sylius/Sylius/issues/10258) Changing shipment state in shipment index ([@AdamKasp](https://github.com/AdamKasp))
- [#10260](https://github.com/Sylius/Sylius/issues/10260) Show order directly from shipments page ([@AdamKasp](https://github.com/AdamKasp))
- [#10271](https://github.com/Sylius/Sylius/issues/10271) select filter + filter shipment by state ([@AdamKasp](https://github.com/AdamKasp))
- [#10281](https://github.com/Sylius/Sylius/issues/10281) Improved: Product fixture (fixed #10272) ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10310](https://github.com/Sylius/Sylius/issues/10310) [PromotionCoupon] Non reusable coupons after cancelling the orders ([@GSadee](https://github.com/GSadee))
- [#10316](https://github.com/Sylius/Sylius/issues/10316) [Admin][Product] Access the variants management from product edit page ([@GSadee](https://github.com/GSadee))
- [#10318](https://github.com/Sylius/Sylius/issues/10318) [Admin][Promotion] Update promotion menu builder name to be consistent with other ([@GSadee](https://github.com/GSadee))
- [#10346](https://github.com/Sylius/Sylius/issues/10346) Fix the master build by requiring ^1.5 Grid & GridBundle ([@pamil](https://github.com/pamil))
- [#10380](https://github.com/Sylius/Sylius/issues/10380) [Behat] Fix duplicate step definition ([@Zales0123](https://github.com/Zales0123))
- [#10410](https://github.com/Sylius/Sylius/issues/10410) Fix typo ([@dnna](https://github.com/dnna))
- [#10496](https://github.com/Sylius/Sylius/issues/10496) [UPGRADE] Mention locale requirement change in UPGRADE-1.5 ([@Zales0123](https://github.com/Zales0123))

## v1.5.2 (2019-06-20)

#### Details

- [#10191](https://github.com/Sylius/Sylius/issues/10191) [taxon_fixtures] Fix child taxon slug generation ([@tannyl](https://github.com/tannyl))
- [#10371](https://github.com/Sylius/Sylius/issues/10371) [Docs] How to find out the resource config required when customizing models ([@4c0n](https://github.com/4c0n))
- [#10384](https://github.com/Sylius/Sylius/issues/10384) "Getting Started with Sylius" guide ([@Zales0123](https://github.com/Zales0123), [@CoderMaggie](https://github.com/CoderMaggie))
- [#10389](https://github.com/Sylius/Sylius/issues/10389) [UI] Hide filters by default on index pages ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10404](https://github.com/Sylius/Sylius/issues/10404) Fix huge autocomplete queries issue ([@bitbager](https://github.com/bitbager), [@pamil](https://github.com/pamil))
- [#10410](https://github.com/Sylius/Sylius/issues/10410) Fix typo ([@dnna](https://github.com/dnna))
- [#10412](https://github.com/Sylius/Sylius/issues/10412) [Docs] Added tip for using group sequence validations ([@4c0n](https://github.com/4c0n))
- [#10423](https://github.com/Sylius/Sylius/issues/10423) [Doc] End of bugfix support for 1.3 ([@lchrusciel](https://github.com/lchrusciel))
- [#10426](https://github.com/Sylius/Sylius/issues/10426) Using client from browser kit component instead of http kernel component ([@loevgaard](https://github.com/loevgaard))
- [#10432](https://github.com/Sylius/Sylius/issues/10432) Add known errors section to UPGRADE file ([@pamil](https://github.com/pamil))
- [#10433](https://github.com/Sylius/Sylius/issues/10433) Bump fstream from 1.0.11 to 1.0.12 ([@dependabot](https://github.com/dependabot)[[@bot](https://github.com/bot)])
- [#10440](https://github.com/Sylius/Sylius/issues/10440) Fix removing taxons with numeric codes from products ([@vvasiloi](https://github.com/vvasiloi))
- [#10445](https://github.com/Sylius/Sylius/issues/10445) Fix typos and grammar in the Getting Started guide ([@pamil](https://github.com/pamil))
- [#10446](https://github.com/Sylius/Sylius/issues/10446) Update the 1.1 version status in the release process docs ([@pamil](https://github.com/pamil))
- [#10450](https://github.com/Sylius/Sylius/issues/10450) Fix interfaces mapping in Doctrine for admin user and shop user ([@pamil](https://github.com/pamil))
- [#10462](https://github.com/Sylius/Sylius/issues/10462) [Docs] Update Sylius versions in installation and contribution guides ([@GSadee](https://github.com/GSadee))

## v1.5.1 (2019-05-29)

#### Details

- [#10364](https://github.com/Sylius/Sylius/issues/10364) As an Administrator, I want always to have proper option values selected while editing a product variant ([@Tomanhez](https://github.com/Tomanhez), [@monro93](https://github.com/monro93))
- [#10372](https://github.com/Sylius/Sylius/issues/10372) Image display in edit form ([@AdamKasp](https://github.com/AdamKasp))
- [#10375](https://github.com/Sylius/Sylius/issues/10375) [Docs] Update "Customizing State Machine" ([@AdamKasp](https://github.com/AdamKasp))
- [#10386](https://github.com/Sylius/Sylius/issues/10386) [Build Fix][Behat] Change scenarios to @javascript due to taxon tree changes ([@Zales0123](https://github.com/Zales0123))
- [#10394](https://github.com/Sylius/Sylius/issues/10394) Fix error caused by the taxon tree ([@kulczy](https://github.com/kulczy))
- [#10407](https://github.com/Sylius/Sylius/issues/10407) Bump the Sylius release versions in docs ([@teohhanhui](https://github.com/teohhanhui))
- [#10414](https://github.com/Sylius/Sylius/issues/10414) Use HTTPS links when possible ([@javiereguiluz](https://github.com/javiereguiluz))

## v1.5.0 (2019-05-15)

#### TL;DR

- Extracted packages from the core ([#10325](https://github.com/Sylius/Sylius/issues/10325), [#10326](https://github.com/Sylius/Sylius/issues/10326), [#10327](https://github.com/Sylius/Sylius/issues/10327))
- Added order index API endpoint ([#10161](https://github.com/Sylius/Sylius/issues/10161))
- Added ability to customise whether coupons should be reusable after canceling an order using them ([#10310](https://github.com/Sylius/Sylius/issues/10310))
- Added shipments list view in the admin panel ([#10249](https://github.com/Sylius/Sylius/issues/10249))
- Added ability to define locale used by Sylius during the installation ([#10240](https://github.com/Sylius/Sylius/issues/10240))

#### Details

- [#10069](https://github.com/Sylius/Sylius/issues/10069) [ShopBundle][PayumBundle] FIX payum authorize route ([@JaisDK](https://github.com/JaisDK), [@pamil](https://github.com/pamil), [@lchrusciel](https://github.com/lchrusciel))
- [#10116](https://github.com/Sylius/Sylius/issues/10116) Allow nullable shop billing data ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10121](https://github.com/Sylius/Sylius/issues/10121) [GridBundle] Doc improvement ([@Roshyo](https://github.com/Roshyo))
- [#10149](https://github.com/Sylius/Sylius/issues/10149) Add index on order.cart + order.updated_at for faster expired cart removal selection ([@stefandoorn](https://github.com/stefandoorn))
- [#10161](https://github.com/Sylius/Sylius/issues/10161) Orders index API endpoint ([@JaisDK](https://github.com/JaisDK), [@Zales0123](https://github.com/Zales0123))
- [#10163](https://github.com/Sylius/Sylius/issues/10163) [BuildFix] Fix AbstractMigration use statement ([@Zales0123](https://github.com/Zales0123))
- [#10166](https://github.com/Sylius/Sylius/issues/10166) ShopBillingData fixtures ([@Zales0123](https://github.com/Zales0123))
- [#10199](https://github.com/Sylius/Sylius/issues/10199) Allowing options to be given with resource[0].id syntax ([@Roshyo](https://github.com/Roshyo))
- [#10202](https://github.com/Sylius/Sylius/issues/10202) Expanding the customer fixtures ([@mamazu](https://github.com/mamazu))
- [#10209](https://github.com/Sylius/Sylius/issues/10209) [Shop] Use first variant image on a cart page ([@castler](https://github.com/castler), [@Zales0123](https://github.com/Zales0123))
- [#10233](https://github.com/Sylius/Sylius/issues/10233) Payment status at  order history page ([@AdamKasp](https://github.com/AdamKasp))
- [#10234](https://github.com/Sylius/Sylius/issues/10234) Orders shipment status ([@Tomanhez](https://github.com/Tomanhez))
- [#10240](https://github.com/Sylius/Sylius/issues/10240) #9965 Feature/local in sylius install ([@oallain](https://github.com/oallain))
- [#10249](https://github.com/Sylius/Sylius/issues/10249) Browsing shipments ([@AdamKasp](https://github.com/AdamKasp))
- [#10250](https://github.com/Sylius/Sylius/issues/10250) See Manage coupons from template edit promotion  ([@Tomanhez](https://github.com/Tomanhez))
- [#10258](https://github.com/Sylius/Sylius/issues/10258) Changing shipment state in shipment index ([@AdamKasp](https://github.com/AdamKasp))
- [#10260](https://github.com/Sylius/Sylius/issues/10260) Show order directly from shipments page ([@AdamKasp](https://github.com/AdamKasp))
- [#10271](https://github.com/Sylius/Sylius/issues/10271) select filter + filter shipment by state ([@AdamKasp](https://github.com/AdamKasp))
- [#10281](https://github.com/Sylius/Sylius/issues/10281) Improved: Product fixture (fixed #10272) ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10310](https://github.com/Sylius/Sylius/issues/10310) [PromotionCoupon] Non reusable coupons after cancelling the orders ([@GSadee](https://github.com/GSadee))
- [#10316](https://github.com/Sylius/Sylius/issues/10316) [Admin][Product] Access the variants management from product edit page ([@GSadee](https://github.com/GSadee))
- [#10318](https://github.com/Sylius/Sylius/issues/10318) [Admin][Promotion] Update promotion menu builder name to be consistent with other ([@GSadee](https://github.com/GSadee))
- [#10346](https://github.com/Sylius/Sylius/issues/10346) Fix the master build by requiring ^1.5 Grid & GridBundle ([@pamil](https://github.com/pamil))

## v1.5.0-RC.1 (2019-05-07)

#### TL;DR

Will be provided for the stable release.

#### Details

- [#10069](https://github.com/Sylius/Sylius/issues/10069) [ShopBundle][PayumBundle] FIX payum authorize route ([@JaisDK](https://github.com/JaisDK), [@pamil](https://github.com/pamil), [@lchrusciel](https://github.com/lchrusciel))
- [#10116](https://github.com/Sylius/Sylius/issues/10116) Allow nullable shop billing data ([@Zales0123](https://github.com/Zales0123), [@pamil](https://github.com/pamil))
- [#10121](https://github.com/Sylius/Sylius/issues/10121) [GridBundle] Doc improvement ([@Roshyo](https://github.com/Roshyo))
- [#10149](https://github.com/Sylius/Sylius/issues/10149) Add index on order.cart + order.updated_at for faster expired cart removal selection ([@stefandoorn](https://github.com/stefandoorn))
- [#10161](https://github.com/Sylius/Sylius/issues/10161) Orders index API endpoint ([@JaisDK](https://github.com/JaisDK), [@Zales0123](https://github.com/Zales0123))
- [#10163](https://github.com/Sylius/Sylius/issues/10163) [BuildFix] Fix AbstractMigration use statement ([@Zales0123](https://github.com/Zales0123))
- [#10166](https://github.com/Sylius/Sylius/issues/10166) ShopBillingData fixtures ([@Zales0123](https://github.com/Zales0123))
- [#10199](https://github.com/Sylius/Sylius/issues/10199) Allowing options to be given with resource[0].id syntax ([@Roshyo](https://github.com/Roshyo))
- [#10202](https://github.com/Sylius/Sylius/issues/10202) Expanding the customer fixtures ([@mamazu](https://github.com/mamazu))
- [#10209](https://github.com/Sylius/Sylius/issues/10209) [Shop] Use first variant image on a cart page ([@castler](https://github.com/castler), [@Zales0123](https://github.com/Zales0123))
- [#10212](https://github.com/Sylius/Sylius/issues/10212) Update UPGRADE-1.3.md diff link ([@oallain](https://github.com/oallain))
- [#10233](https://github.com/Sylius/Sylius/issues/10233) Payment status at  order history page ([@AdamKasp](https://github.com/AdamKasp))
- [#10234](https://github.com/Sylius/Sylius/issues/10234) Orders shipment status ([@Tomanhez](https://github.com/Tomanhez))
- [#10240](https://github.com/Sylius/Sylius/issues/10240) #9965 Feature/local in sylius install ([@oallain](https://github.com/oallain))
- [#10249](https://github.com/Sylius/Sylius/issues/10249) Browsing shipments ([@AdamKasp](https://github.com/AdamKasp))
- [#10250](https://github.com/Sylius/Sylius/issues/10250) See Manage coupons from template edit promotion  ([@Tomanhez](https://github.com/Tomanhez))
- [#10258](https://github.com/Sylius/Sylius/issues/10258) Changing shipment state in shipment index ([@AdamKasp](https://github.com/AdamKasp))
- [#10260](https://github.com/Sylius/Sylius/issues/10260) Show order directly from shipments page ([@AdamKasp](https://github.com/AdamKasp))
- [#10271](https://github.com/Sylius/Sylius/issues/10271) select filter + filter shipment by state ([@AdamKasp](https://github.com/AdamKasp))
- [#10281](https://github.com/Sylius/Sylius/issues/10281) Improved: Product fixture (fixed #10272) ([@igormukhingmailcom](https://github.com/igormukhingmailcom))
- [#10310](https://github.com/Sylius/Sylius/issues/10310) [PromotionCoupon] Non reusable coupons after cancelling the orders ([@GSadee](https://github.com/GSadee))
- [#10316](https://github.com/Sylius/Sylius/issues/10316) [Admin][Product] Access the variants management from product edit page ([@GSadee](https://github.com/GSadee))
- [#10318](https://github.com/Sylius/Sylius/issues/10318) [Admin][Promotion] Update promotion menu builder name to be consistent with other ([@GSadee](https://github.com/GSadee))
- [#10346](https://github.com/Sylius/Sylius/issues/10346) Fix the master build by requiring ^1.5 Grid & GridBundle ([@pamil](https://github.com/pamil))
