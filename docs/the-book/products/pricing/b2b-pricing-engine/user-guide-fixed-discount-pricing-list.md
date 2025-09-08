# User Guide: Fixed Discount Pricing List

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-start (1).png" alt=""><figcaption></figcaption></figure>

### Overview

This guide shows how to create a **fixed pricing list**, which allows you to define exact prices for individual product variants. This is ideal for B2B agreements where specific **Customer Groups** or O**rganizations** have negotiated fixed product prices.

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

    * Select a **Product Varian** from autocomplete
    * Set the exact **Price**
    *   _(Optional)_ Set a **Minimum Price** if applicable\


        {% hint style="info" %}
        You can add multiple Product Variants and prices. Just click the **Add** button
        {% endhint %}



        <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 07.45.15.png" alt=""><figcaption></figcaption></figure>
6. **Save**\
   Click **Create** to activate the pricing list

{% hint style="info" %}
A **Customer Group** or **Organization** can be assigned to only **one pricing list at a time**.\
If you add a group or organization that is already linked to another price list, it will be **automatically removed from the previous one**.
{% endhint %}

### Results

When a customer from the assigned **Customer Group (**_**Wholesale** in the example)_ or **Organization** logs in, they will see product prices replaced by the values defined in this pricing list.

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 07.55.48.png" alt=""><figcaption></figcaption></figure>

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-end.png" alt=""><figcaption></figcaption></figure>
