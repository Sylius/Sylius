# CHANGELOG FOR `1.5.X`

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
