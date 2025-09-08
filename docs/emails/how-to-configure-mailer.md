# How to configure mailer?

Sylius uses [`SyliusMailerBundle`](https://github.com/Sylius/SyliusMailerBundle/blob/v2.1.0/docs/index.md), which is based on the **Symfony Mailer** to provide a powerful and customizable email system. This allows you to configure easily:

* Environment-based mail transports
* Email localization
* Named email templates

***

## 🔧 Basic Setup

Sylius relies on Symfony's `MAILER_DSN` system to define the email provider. You’ll usually define this in your `.env` files.

### Example (Using Mailtrap):

```env
MAILER_DSN=smtp://username:password@smtp.mailtrap.io:2525
```

{% hint style="success" %}
To explore supported providers and DSN formats, refer to the official [Symfony Mailer transport documentation](https://symfony.com/doc/current/mailer.html#using-built-in-transports).
{% endhint %}

***

## ✅ Example Configuration per Environment

### Production (`.env.prod`)

In production, just provide a valid DSN.

<pre class="language-env"><code class="lang-env"><strong># env.prod
</strong><strong>
</strong><strong>MAILER_DSN=smtp://apikey:your-api-key@sendgrid
</strong></code></pre>

No extra steps are needed unless you want to customize delivery logic (e.g., retry policies, queuing, etc.).

***

## Development & Testing (`.env.local` / `.env.test`)

Two common options:

### **Option A: Disable Email Delivery** (Defaul&#x74;**)**

```bash
# for example env.local

MAILER_DSN=null://default
```

Emails won’t be sent — great for running automated tests without side effects.

### **Option B: Use MailHog (Visual Email Debugger)**

```bash
# for example env.local

MAILER_DSN=smtp://localhost:1025
```

Launch MailHog and view emails at [http://localhost:8025](http://localhost:8025).

{% hint style="danger" %}
## Security Reminder

Never commit secrets (API keys, SMTP passwords) to your repository.\
Use environment variables or Symfony Secrets Vault for safe management
{% endhint %}
