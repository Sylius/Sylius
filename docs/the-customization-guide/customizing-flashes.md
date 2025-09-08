# Customizing Flashes

Flash messages in Sylius provide feedback after actions like adding resources in the admin panel or registering in the shop. You can easily customize them to better fit your needs.

### Why Customize Flash Messages?

You may want to modify flash messages to:\
✅ Change confirmation messages, e.g., **"Your email has been successfully verified." → "You have successfully verified your email."**\
✅ Adjust success or error messages to align with your branding.\
✅ Improve clarity for users in different locales.

### How to Customize Flash Messages

#### **Step 1: Create a Flash Translation File**

If you haven’t already, create `translations/flashes.en.yaml` for English flash messages.

📌 **For other languages**, create separate files, such as:

* **Polish** → `translations/flashes.pl.yaml`
* **French** → `translations/flashes.fr.yaml`

#### **Step 2: Define Custom Flash Messages**

Find the relevant **flash key** and override its text.

**Example:** Changing the flash message for email verification:

```yaml
sylius:
    user:
        verify_email: 'You have successfully verified your email.'
```

To apply your changes, clear the cache:

```bash
php bin/console cache:clear
```

### Good to Know

✅ Flash messages can be customized **directly in your application** or **in a Sylius plugin**.\
✅ Different languages should be stored in separate **flashes.\[locale].yaml** files.

With these steps, you can easily personalize Sylius flash messages to improve user experience! 🚀
