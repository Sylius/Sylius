# ➕ B2B Pricing Engine

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-start (1).png" alt=""><figcaption></figcaption></figure>

The Sylius Plus B2B module introduces a powerful and flexible **Pricing Engine** designed to support advanced pricing strategies for business customers. Merchants can define and assign **custom price lists** that reflect negotiated terms or segment-specific offers.

A price list can:

* Apply a uniform **percentage discount** (e.g., 5%) to all products in the catalog.
* Define **custom prices** for selected product variants.
* Define **tier prices** for selected product variants.

Additional capabilities include:

* Assigning price lists to specific **Customer Groups** or **Organizations**, ensuring the correct pricing is applied automatically at checkout.
* Supporting **scalable and precise pricing strategies**, ideal for B2B, wholesale, or contract-driven business models.

This feature helps streamline complex pricing management while remaining fully integrated within the Sylius ecosystem.

### Full API support

**Full API support** is provided for managing price lists, including creating, updating, assigning, and deleting pricing rules. This ensures seamless integration with headless frontends, external ERP systems, or any custom B2B solutions built on top of Sylius.

### Common use cases

#### Taking Advantage of the Sylius Admin Panel

For **smaller B2B enterprises**, the Sylius Admin Panel provides an intuitive and flexible way to manage pricing lists without any developer involvement. Merchants can create price lists, define percentage discounts, assign fixed prices to specific product variants, and configure tiered pricing - all through the built-in UI.&#x20;

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-06-18 at 06.50.52.png" alt=""><figcaption></figcaption></figure>

This approach offers a powerful yet accessible solution for teams that prefer hands-on control over their B2B pricing strategy directly from the Sylius backend.

#### Using the API to Feed Sylius Pricing Information

For **larger businesses** with more advanced infrastructure, Sylius offers **full API support** for the Pricing Engine. This enables automated management of pricing lists through custom integrations with ERP, PIM, CRM, or other external systems.&#x20;

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-06-18 at 06.52.46.png" alt=""><figcaption></figcaption></figure>

All operations can be performed programmatically, making it ideal for headless commerce setups or businesses operating across multiple digital channels.

#### Using the Synchronization Solution – Import & Export

The B2B plugin also comes with a built-in **import/export mechanism** supporting CSV, XML, and SQL formats. This is particularly useful for businesses that manage their pricing in spreadsheets or external systems and want to sync those with Sylius. The import/export solution allows batch updates, version control, and easier data portability.&#x20;

<figure><img src="../../../../.gitbook/assets/Screenshot 2025-06-18 at 06.55.34.png" alt=""><figcaption></figcaption></figure>

A working example of this synchronization approach is available in the **Sylius B2B Test Application**, helping teams to quickly adapt and extend the feature to their specific needs.

<figure><img src="../../../../.gitbook/assets/sylius-docs-plusfeature-end.png" alt=""><figcaption></figcaption></figure>
