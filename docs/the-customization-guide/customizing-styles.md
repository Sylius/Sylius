# Customizing Styles

In Sylius 2.x, customizing the visual appearance of the admin and shop interfaces is cleanly handled through CSS variables defined by the frontend frameworks used by each section:

* **Admin Panel**: Uses [Tabler UI](https://tabler.io/) variables, exposing mostly by `--tblr-*`&#x20;
* **Shop Frontend**: Uses Bootstrap variables, exposing mostly by `--bs-*`&#x20;

This guide will walk you through how to locate, identify, and override these variables to implement custom themes or brand-aligned styles.

***

### 1. Identifying the Element to Customize

Before making any changes, you should identify the exact UI element you'd like to customize:

1. Open the relevant interface (Admin or Shop) in your browser.
2. Use **Developer Tools** (Right-click → Inspect)
3. In the **Styles** panel, look for applied custom properties (CSS variables), such as:

```scss
--tblr-primary: #206bc4;     /* Admin interface */
--bs-btn-bg: #0d6efd;        /* Shop interface */
```

These variables are usually defined globally via the `:root`, `body`, or `*` selectors.\
Once identified, you can override them in your own stylesheets to implement the desired look.

***

### 2. Overriding Admin Theme

To customize the admin layout theme (e.g., button colors, backgrounds, etc.).

#### Example 1: Change Primary Button Color in Admin

Let's inspect the **Create** button on the Taxon page. A few key facts:

* The button class is `.btn-primary`
* It relies on the variables `--tblr-btn-bg` and `--tblr-btn-hover-bg`

<figure><img src="../.gitbook/assets/image (23).png" alt=""><figcaption></figcaption></figure>

Create or edit a custom stylesheet:

```scss
/* assets/admin/styles/custom.scss */

.btn-primary {
    --tblr-btn-bg: #FF0000;
    --tblr-btn-hover-bg: #FF5531;
}
```

Then include it in your admin entrypoint:

```javascript
// assets/admin/entrypoint.js

import './styles/custom.scss';
```

And build / rebuild your assets:

```bash
yarn build # or yarn watch
```

**Result**:&#x20;

<figure><img src="../.gitbook/assets/image (25).png" alt=""><figcaption></figcaption></figure>

All the primary buttons are now red! :tada:

#### Example 2: Overriding the Base Primary Color in Admin

Inspect the button again:

* `--tblr-btn-bg` uses `--tblr-primary`
* `--tblr-btn-hover-bg` uses `--tblr-primary-darken`

<figure><img src="../.gitbook/assets/image (24).png" alt=""><figcaption></figcaption></figure>

Override these globally:

```scss
/* assets/admin/styles/custom.scss */

* {
    --tblr-primary: #FF0000;
    --tblr-primary-darken: #FF5531;
}
```

Rebuild assets:

```bash
yarn build # or yarn watch
```

**Result**:&#x20;

<figure><img src="../.gitbook/assets/image (28).png" alt=""><figcaption></figcaption></figure>

All the base layout colors have been changed to red! :tada:

***

### 3. Overriding Shop Theme

The shop frontend uses Bootstrap, and its buttons rely on variables like `--bs-btn-bg` and `--bs-btn-hover-bg`.

#### Example 1: Change Primary Button Color in Shop

Inspect a primary button in the shop (e.g., "Add to cart") and note:

* Class: `.btn-primary`
* Variables: `--bs-btn-bg`, `--bs-btn-hover-bg`

<figure><img src="../.gitbook/assets/image (20).png" alt=""><figcaption></figcaption></figure>

Override them like this:

```scss
/* assets/shop/styles/custom.scss */

.btn-primary {
    --bs-btn-bg: #FF0000;
    --bs-btn-hover-bg: #FF5531;
}
```

Include this file in your shop entrypoint:

```javascript
// assets/shop/entrypoint.js

import './styles/custom.scss';
```

Rebuild your assets:

```bash
yarn build # or yarn watch
```

**Result**:&#x20;

<figure><img src="../.gitbook/assets/image (21).png" alt=""><figcaption></figcaption></figure>

All primary buttons in the shop are now red! :tada:

#### Example 2: Overriding the Base Primary Color in Shop

Sometimes it’s more effective to override the **base** variables used throughout Bootstrap, rather than styling individual components. However, the Shop theme is more granular than the Admin, so a few extra steps may be needed.

If you want to change the base primary color (e.g., from teal to red) across the entire shop, you’ll need to override several related variables. This includes buttons, links, and navigation elements—each of which may use distinct variables.

Here’s a comprehensive override example:

```scss
/* assets/shop/styles/custom.scss */

* {
  --bs-btn-bg: #FF0000 !important;              // All buttons
  --bs-btn-hover-bg: #FF5531 !important;        // Button hover state

  --bs-primary-rgb: 255, 0, 0;                   // RGB fallback
  --bs-primary: #FF0000;                         // Primary theme color

  --bs-link-color-rgb: 255, 0, 0;                // Link RGB
  --bs-link-color: #FF0000 !important;           // Link color (non-underlined)
  --bs-link-hover-color: #FF5531 !important;     // Link hover color
  --bs-link-hover-color-rgb: 255, 85, 49 !important;

  --bs-navbar-hover-color: #FF5531;              // Navbar hover links
  --bs-navbar-active-color: #FF5531;             // Navbar active link
}
```

{% hint style="warning" %}
Using `!important` is not recommended in general, but may be necessary when overriding styles set on `*` or `:root`.
{% endhint %}

If you don’t want to search for every individual variable—or if customizations don’t apply as expected—you can target element values directly. However, this is **not** the preferred approach:

```scss
/* assets/shop/styles/custom.scss */

a:hover {
    color: #FF5531 !important;
}
```

Rebuild assets:

```bash
yarn build # or yarn watch
```

**Result**:

<figure><img src="../.gitbook/assets/image (22).png" alt=""><figcaption></figcaption></figure>

The primary theme color across the shop is now red! :tada:

***

### 4. Scoping and Specificity

As you’ve seen throughout this guide, the key to customizing your frontend styles is identifying the correct variables through inspection and then overriding them in your own stylesheets.

If you want to customize the grid system, spacing, or any other part of Sylius, simply inspect the element and update the relevant CSS variables accordingly.

To limit changes to specific parts of your application, consider scoping your overrides more precisely—for example, by targeting only the components you want to customize.

{% hint style="warning" %}
**Clear Symfony and Browser Caches After Building Assets**

If your style changes don’t appear after running `yarn build`, it’s likely due to caching. Symfony’s HTTP cache and your browser’s cache can both serve outdated assets.

1. **Clear the Symfony cache**:

```bash
php bin/console cache:clear
```

2. **Clear your browser cache** (or try a hard refresh, typically `Ctrl + Shift + R` or `Cmd + Shift + R`).

These steps help ensure you’re seeing the most recent version of your styles, especially in development environments.
{% endhint %}

