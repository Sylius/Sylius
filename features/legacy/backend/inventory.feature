@legacy @inventory
Feature: Inventory tracking
    In order track and control my inventory
    As a store owner
    I want to be able to manage stock levels and availability

    Background:
        Given store has default configuration
        And there are following options:
            | code | name          | values                          |
            | O1   | T-Shirt color | Red[OV1], Blue[OV2], Green[OV3] |
            | O2   | T-Shirt size  | S[OV4], M[OV5], L[OV6]          |
        And the following products exist:
            | name          | price | options |
            | Super T-Shirt | 19.99 | O2, O1  |
            | Black T-Shirt | 19.99 | O2      |
            | Mug           | 5.99  |         |
            | Sticker       | 10.00 |         |
        And I am logged in as administrator

    Scenario: Seeing index of inventory
        Given I am on the dashboard page
        When I follow "Inventory levels"
        Then I should be on the inventory index page
        And I should see 8 stockables in the list

    Scenario: Seeing empty index of inventory
        Given there are no products
        When I am on the inventory index page
        Then I should see "There are no products to display"

    Scenario: Updating product stock level
        Given I am on the page of product "Sticker"
        When I click "Edit" near "STICKER-VARIANT"
        When I fill in "Current stock" with "10"
        And I press "Save changes"
        Then I should be on the page of product "Sticker"
        And I should see "Variant has been successfully updated"

    Scenario: Making product not available on demand
        Given I am on the page of product "Sticker"
        When I click "Edit" near "STICKER-VARIANT"
        When I uncheck "Available on demand"
        And I press "Save changes"
        Then I should be on the page of product "Sticker"
        And I should see "Variant has been successfully updated"

    Scenario: Updating variant stock level
        Given product "Black T-Shirt" is available in all variations
        And I am on the page of product "Black T-Shirt"
        When I click "Edit" near "T-Shirt size: L"
        And I fill in "Current stock" with "10"
        And I press "Save changes"
        Then I should be on the page of product "Black T-Shirt"
        And I should see "Variant has been successfully updated"

    Scenario: Making variant not available on demand
        Given product "Black T-Shirt" is available in all variations
        And I am on the page of product "Black T-Shirt"
        When I click "Edit" near "T-Shirt size: L"
        And I uncheck "Available on demand"
        And I press "Save changes"
        Then I should be on the page of product "Black T-Shirt"
        And I should see "Variant has been successfully updated"
