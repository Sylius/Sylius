@checkout
Feature: Seeing current prices of products after catalog promotion becomes ineligible
    In order to buy products in its correct prices
    As a Customer
    I want to have products with its current prices in the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt"
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "United States" channel
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a catalog promotion "Winter sale" available in "United States" channel that reduces price by "25%" and applies on "PHP T-Shirt" variant
        And I am a logged in customer

    @ui @api
    Scenario: Processing order with valid prices after catalog promotion becomes ineligibly
        Given I have "PHP T-Shirt" variant of this product in the cart
        When the "Winter sale" catalog promotion is no longer available
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed through checkout process
        Then I should be on the checkout summary step
        And I should see product "T-Shirt" with unit price "$20.00"
