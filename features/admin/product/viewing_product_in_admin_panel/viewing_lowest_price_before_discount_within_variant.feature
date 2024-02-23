@viewing_product_in_admin_panel
Feature: Seeing the lowest price before the discount within variant
    In order to be aware of variant's prices
    As an Administrator
    I want to see details of the lowest price before the discount nearby variant's price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Rye" variant priced at "$9.00"
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Potato" variant priced at "$11.00"
        And this variant's price changed to "$21.00" and original price changed to "$37.00"
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Seeing price block with lowest price before the discount within variant
        When I access the "Wyborowa Vodka" product
        Then I should not see the lowest price before the discount for "Wyborowa Vodka Rye" variant in "United States" channel
        And I should see the lowest price before the discount of "$11.00" for "Wyborowa Vodka Potato" variant in "United States" channel
