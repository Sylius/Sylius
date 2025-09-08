# User Guide: Fixed Discount Pricing List with tier pricing

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-start (1).png" alt=""><figcaption></figcaption></figure>

### Overview

This guide shows how to create a **tier pricing list**, which allows you to define discounted prices for specific product variants based on purchased quantity. This is ideal for B2B setups where you want to offer **volume-based discounts** to **Customer groups** or **Organizations**.

Tier pricing is an extension of fixed pricing; you can add **one or more tier prices** that apply when the customer purchases a specified quantity.

### Step-by-step Instructions

1.  **Navigate to Pricing Engine → Pricing Lists**\
    In the Sylius Admin Panel, open the **Pricing Engine** section and go to **Pricing Lists**.\


    <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 06.15.50 (1).png" alt=""><figcaption></figcaption></figure>
2.  **Click Create and** **Choose Fixed Discount**\
    Hit the **Create** button and select **Fixed discount pricing list** from the dropdown.\


    <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 06.18.56.png" alt=""><figcaption></figcaption></figure>
3. **Fill in the General Section**
   * **Name**: e.g., Partner Special Prices
   * **Description** (optional)
   *   **Enabled**: Check the box to activate this price list\


       <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 07.41.30.png" alt=""><figcaption></figcaption></figure>
4. **Assign to Target Audience**
   * Use the **Customer Groups** or **Organizations** fields to define who this pricing should apply to
5.  **Define Fixed Prices**\


    In the **Product variant prices** section:

    * Select a **Product Varian** from the autocomplete
    * Set the exact **Price**
    *   _(Optional)_ Set a **Minimum Price** if applicable\


        In the **Tier prices** area:

        * Click **Add**
        * For each tier, define:
          * **Quantity** (e.g., `10`)
          * **Price** to apply when that quantity is reached

        <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 08.15.24.png" alt=""><figcaption></figcaption></figure>

        {% hint style="info" %}
        You can add multiple tier prices for each product variant. Just click the **Add** button
        {% endhint %}
6. **Save**\
   Click **Create** to activate the pricing list

{% hint style="info" %}
A **Customer Group** or **Organization** can be assigned to only **one pricing list at a time**.\
If you add a group or organization that is already linked to another price list, it will be **automatically removed from the previous one**.
{% endhint %}

### Results

When a customer from the assigned **Customer Group (**_**Wholesale** in the example)_ or **Organization** logs in, they will see product prices replaced by the values defined in this pricing list.

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 07.55.48.png" alt=""><figcaption><p>Wild fig dress product page</p></figcaption></figure>

If their cart quantity meets a tier level, the matching tier price will be applied.

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 08.08.53.png" alt=""><figcaption><p>Cart with 4 items of Wild Fig Dress</p></figcaption></figure>

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 08.09.04.png" alt=""><figcaption><p>Cart with 5 items of Wild Fig Dress</p></figcaption></figure>

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 08.09.14.png" alt=""><figcaption><p>Cart with 10 items of Wild Fig Dress</p></figcaption></figure>

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-end.png" alt=""><figcaption></figcaption></figure>
