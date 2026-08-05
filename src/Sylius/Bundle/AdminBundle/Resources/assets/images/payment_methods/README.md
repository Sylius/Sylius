# Payment method icons

These icons are rendered as small "chips" on the payment method promo banners
(`templates/payment_method/index/content/banner/slide.html.twig`) and in the payment method grid rows
(`.../grid/data_table/_item.html.twig`), through the `shared/helper/payment_method_icon.html.twig` macro.

## Conventions

- One SVG per method, named after the method code used in
  `Resources/config/app/twig_hooks/payment_method/index.yaml` (`snake_case`, e.g. `apple_pay.svg`).
- `viewBox="0 0 36 24"` with a baked-in white `<rect rx="4">` plate, so the mark stays legible on both
  light and dark surfaces.
- Each icon must read at two sizes: `2.7rem` tall on the banners and `1.7rem` in the grid rows.
- Webpack Encore flattens all of `Resources/assets/images/**` into `build/admin/images/`, so a basename
  here may not collide with one in `payment_partners/` **using the same extension**.

## ⚠️ Placeholders pending replacement

Every icon here is an official brand mark, normalised to the house treatment.

### Variants worth improving

These are real marks, but the supplied lockups get tight at the 27px grid size:

- `bancontact` — a **vertical** lockup (mark above wordmark), so the wordmark blurs at 27px.
  A horizontal variant would read better.
- `ideal` — the horizontal iDEAL/wero lockup, carrying its own yellow plate. Both wordmarks are small at
  27px. Consider an iDEAL-only mark, and decide whether the chip should still be labelled "iDEAL" now
  that the brand is merging into wero.

Do not fix these by cropping the mark — cutting one brand out of a lockup modifies it and breaks every
brand's guidelines. Download the correct variant instead. Trimming a canvas down to the artwork bounds
is fine, and was done for the partner logos in `../payment_partners/`.

Two marks were considered and dropped, so there are no files for them: `link` (Stripe's own one-click
wallet) and `sepa`. Every icon in this directory is referenced by at least one banner in
`Resources/config/app/twig_hooks/payment_method/index.yaml`; keep it that way.

## Trademark note

These are third-party trademarks used to identify the payment methods each provider supports. Several
carry restrictive usage guidelines — **Apple Pay and Google Pay in particular** specify minimum sizes,
clear space and a prohibition on altering the mark. Confirm compliance for every mark before shipping.

`LICENSE_OF_TRADEMARK_AND_LOGO` at the bundle root covers Sylius's own marks only and does not apply here.
