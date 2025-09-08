# Contributing Translations

Sylius supports multiple languages, and we rely on the community to help maintain and improve translations. Even small contributions, like fixing a typo or translating a new string, can make a big impact for users around the world.

## 📁 Where Translations Live

Translation files in Sylius follow Symfony’s structure and are spread across many packages and bundles, not just in a single directory.

You'll find them in multiple `translations/` directories such as:

* `src/Sylius/AdminBundle/Resources/translations/`
* `src/Sylius/ShopBundle/Resources/translations/`
* `src/Sylius/CoreBundle/Resources/translations/`
* `src/Sylius/ApiBundle/Resources/translations/`

Each file is named using the convention:

```
messages.{locale}.yaml
```

For example, French admin UI strings live in:

```
src/Sylius/AdminBundle/Resources/translations/messages.fr.yaml
```

{% hint style="info" %}
If unsure where a particular translation string comes from, search the project for the English default key or browse by bundle based on context (e.g., Admin, Shop, API).
{% endhint %}

## Use the GitHub UI

If you're fixing or improving a small number of strings, we **strongly recommend using the GitHub interface**. This is the fastest, simplest way to contribute translations.

**🔧 Step-by-Step: Edit via GitHub**

1. **Go to the Sylius GitHub repository**:\
   → [https://github.com/Sylius/Sylius](https://github.com/Sylius/Sylius)
2. **Switch to the proper version branch**\
   At the top left of the repository page, open the branch selector and:
   * Choose the **lowest supported minor version** where your change is missing (e.g.,  `1.14`, or `2.0`).
   * This increases the chance your translation will be included in a **patch release** and propagated upward via merges.
   * 🧠 Tip: Check the [Release Cycle docs](../../release-cycle/) to identify currently maintained versions.
3.  **Browse to the translation file**\
    Use GitHub’s file browser or press `t` to search for the path quickly. Translation files live under:

    ```
    src/Sylius/*Bundle/Resources/translations/messages.{locale}.yaml
    ```

    For example:

    ```
    src/Sylius/AdminBundle/Resources/translations/messages.pl.yaml
    ```
4. **Click the pencil icon ✏️ to edit**\
   GitHub will automatically fork the repo and create a new branch in your account.
5. **Make your edits**:
   * Ensure proper YAML syntax (indentation, colons, etc.).
   * Do **not** translate placeholders like `%order_number%`.
   * Maintain consistency with existing translations in your language.
6. **Commit your changes**:
   * Enter a clear, descriptive commit message (e.g., `Add Polish translation for refund reason field`).
   * Choose to create a new branch for your commit and start a pull request.
7. **Create your Pull Request**:
   * Provide a descriptive title and explanation of your changes.
   * Indicate that this targets the lowest applicable version to assist reviewers.

### Optional: Contribute via Local Setup

If you're working on many files or want to test your changes in a running Sylius instance, use the guide for [Contributing Code](contributing-code/).

## Translation Guidelines

* ✅ **Be consistent** with existing translations in your language.
* 🛑 **Do not translate placeholders** like `%customer_name%` - These are replaced dynamically.
* ⚠️ Use **YAML 1.2 syntax** with proper indentation.
* 🌐 Try to use **gender-neutral, inclusive language** where appropriate
