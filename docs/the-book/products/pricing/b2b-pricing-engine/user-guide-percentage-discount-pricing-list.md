# User Guide: Percentage Discount Pricing List

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-start (1).png" alt=""><figcaption></figcaption></figure>

### Overview

This guide shows how to create a **percentage discount price list**, which applies a fixed percentage reduction (e.g., 5%, 10%) to all products in the catalog. You can assign this discount to specific **Customer Groups** or **Organizations.** This will allow only selected B2B clients to receive the special pricing.

### Step-by-step Instructions

1.  **Navigate to Pricing Engine → Pricing Lists**\
    In the Sylius Admin Panel, navigate to the **Pricing Engine** section and then select **Pricing Lists**.\


    <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 06.15.50 (1).png" alt=""><figcaption></figcaption></figure>
2.  **Click Create** and **Choose Percentage Discount**\
    Hit the **Create** button and select the **Percentage Discount** pricing list type.\


    <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 06.18.56.png" alt=""><figcaption></figcaption></figure>
3. **Fill in the General Section**
   * **Name**: e.g., _5% discount list_
   * **Description** (optional): _Applies to all catalog products_
   *   **Enabled**: Check the box to activate this price list\


       <figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 06.20.07.png" alt=""><figcaption></figcaption></figure>
4. **Assign to Target Audience**
   * Use the **Customer Groups** or **Organizations** fields to define who this pricing should apply to
5. **Enter the Discount Value**
   * Specify the percentage value to be applied across the catalog (e.g., `5` for a 5% discount)
6. **Save**\
   Click **Create** to activate the pricing list

{% hint style="info" %}
A **Customer Group** or **Organization** can be assigned to only **one pricing list at a time**.\
If you add a group or organization that is already linked to another price list, it will be **automatically removed from the previous one**.
{% endhint %}

### Results

When logged in as a user from the **Acme** organization (or **Retail** customer group), product prices in the catalog are automatically reduced by the configured percentage.\


<figure><img src="../../../../.gitbook/assets/Screenshot 2025-07-02 at 06.22.36.png" alt=""><figcaption></figcaption></figure>

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-end.png" alt=""><figcaption></figcaption></figure>
