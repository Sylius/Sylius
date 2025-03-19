---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Cart flow

Picture this: A user visits your Sylius shop and exclaims, “Someone’s been using my cart! It’s full of items I didn’t add!” Let’s avoid this surprise by exploring how Sylius handles cart functionality.

In Sylius, the cart represents an **order in progress** that hasn’t been placed yet.

{% hint style="info" %}
Each visitor in Sylius has a cart. The cart can be cleared in three ways, by:\
\- placing an order\
\- removing items manually\
\- using a cart-clearing command
{% endhint %}

The cart flow varies depending on whether the user is logged in and what’s already in the cart.

#### First scenario:

```gherkin
Given there is a not logged in user
And this user adds a blue T-Shirt to the cart
And there is a customer identified by email "sylius@example.com"
And the "sylius@example.com" customer has a previously created cart with a red Cap in it
When the not logged in user logs in using "sylius@example.com" email
Then the cart created by a not logged in user should be dropped
And the cart previously created by the user identified by "sylius@example.com" should be set as the current one
And the "sylius@example.com" customer's cart should have a red Cap in it
```

#### Second scenario:

```gherkin
Given there is a not logged in user
And this user adds a blue T-Shirt to the cart
And there is a customer identified by email "sylius@example.com" with an empty cart
When the not logged in user logs in using "sylius@example.com" email
Then the cart created by a not logged in user should not be dropped
And the "sylius@example.com" customer's cart should have a blue T-Shirt in it
```

#### Third scenario:

```gherkin
Given there is a customer identified by email "sylius@example.com" with an empty cart
And this user adds a blue T-Shirt to the cart
When the user logs out
And views the cart
Then the cart should be empty
```

{% hint style="info" %}
The cart mentioned in the third scenario will be available once the customer logs in again.
{% endhint %}
