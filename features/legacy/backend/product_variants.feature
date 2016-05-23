@legacy @product
Feature: Product variants
    In order to add different product variations to my offer
    As a store owner
    I want to be able to manage product variants

    Background:
        Given store has default configuration
        And there are following options:
            | code | name          | values                          |
            | O1   | T-Shirt color | Red[OV1], Blue[OV2], Green[OV3] |
            | O2   | T-Shirt size  | S[OV4], M[OV5], L[OV6]          |
        And the following products exist:
            | name           | price | options |
            | Super T-Shirt  | 19.99 | O2, O1  |
            | Black T-Shirt  | 19.99 | O2      |
            | Sylius T-Shirt | 12.99 | O2, O1  |
            | Mug            | 5.99  |         |
            | Sticker        | 10.00 |         |
        And product "Super T-Shirt" is available in all variations
        And product "Black T-Shirt" has no variants
        And product "Sylius T-Shirt" has no variants
        And I am logged in as administrator

    Scenario: Viewing a product without options
        Given I am on the product index page
        When I click "Sticker"
        Then I should see "There are no options for this product"

    Scenario: Viewing a product with options but without variants
        Given I am on the page of product "Black T-Shirt"
        Then I should not see "There are no options for this product"
        But I should see "There are no variants to display"

    Scenario: Accessing the variant creation form
        Given I am on the page of product "Black T-Shirt"
        When I follow "Create variant"
        Then I should be creating variant of "Black T-Shirt"

    Scenario: Submitting form without the price
        Given I am creating variant of "Black T-Shirt"
        When I press "Create"
        Then I should see "Please enter the price"

    Scenario: Trying to create variant with existing options combination
        Given I am creating variant of "Super T-Shirt"
        When I press "Create"
        Then I should see "Variant with this option set already exists"

    Scenario: Trying to create product variant with invalid price
        Given I am creating variant of "Black T-Shirt"
        When I fill in "Price" with "-0.01"
        And I press "Create"
        Then I should see "Price must not be negative"

    Scenario: Trying to create product variant with invalid original price
        Given I am creating variant of "Black T-Shirt"
        When I fill in "Price" with "1.00"
        And I fill in "Original price" with "-0.01"
        And I press "Create"
        Then I should see "Original price must not be negative"

    Scenario: Displaying the "Generate variants" button
            only for products with options
        Given I am viewing product "Mug"
        Then I should not see "Generate variants"

    Scenario: Generating all possible variants of product
        Given I am viewing product "Black T-Shirt"
        When I follow "Generate variants"
        And I fill in the following:
            | sylius_product_variant_generation_variants_0_code  | T_SHIRT_S |
            | sylius_product_variant_generation_variants_1_code  | T_SHIRT_M |
            | sylius_product_variant_generation_variants_2_code  | T_SHIRT_L |
            | sylius_product_variant_generation_variants_0_price | 100.00    |
            | sylius_product_variant_generation_variants_1_price | 150.00    |
            | sylius_product_variant_generation_variants_2_price | 200.00    |
        And I press "Save changes"
        Then I should still be on the page of product "Black T-Shirt"
        And I should see "Product has been successfully updated."
        And I should see 3 variants in the list

    Scenario: Creating a product variant by selecting option
        Given I am creating variant of "Black T-Shirt"
        When I fill in "Price" with "19.99"
        And I fill in "Code" with "T_SHIRT"
        And I select "L" from "T-Shirt size"
        And I press "Create"
        Then I should be on the page of product "Black T-Shirt"
        And I should see "Variant has been successfully created"

    Scenario: Creating a product variant by selecting multiple options
        Given I am creating variant of "Sylius T-Shirt"
        When I fill in "Price" with "19.99"
        And I fill in "Code" with "T_SHIRT"
        And I select "L" from "T-Shirt size"
        And I select "Red" from "T-Shirt color"
        And I press "Create"
        Then I should be on the page of product "Sylius T-Shirt"
        And I should see "Variant has been successfully created"

    Scenario: Updating the variant price
        Given product "Black T-Shirt" is available in all variations
        And I am on the page of product "Black T-Shirt"
        When I click "Edit" near "T-Shirt size: L"
        And I fill in "Price" with "33.99"
        And I press "Save changes"
        Then I should be on the page of product "Black T-Shirt"
        And I should see "Variant has been successfully updated"

    @javascript
    Scenario: Deleting product variant
        Given product "Black T-Shirt" is available in all variations
        And I am on the page of product "Black T-Shirt"
        When I click "Delete" near "T-Shirt size: L"
        And I click "Delete" from the confirmation modal
        Then I should be on the page of product "Black T-Shirt"
        And I should see "Variant has been successfully deleted"
