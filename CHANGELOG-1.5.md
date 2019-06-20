# CHANGELOG FOR `1.5.X`

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
