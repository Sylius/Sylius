@viewing_product_in_admin_panel
Feature: Seeing the lowest price before the discount for a simple product
    In order to be aware of simple product prices
    As an Administrator
    I want to see details of the lowest price before the discount nearby product's price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Bizon Z056" priced at "$42.00" in "United States" channel
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Seeing price block with lowest price before the discount
        Given this product's price changed to "$21.00" and original price changed to "$37.00"
        When I access the "Bizon Z056" product
        Then I should see "$42.00" as its lowest price before the discount in "United States" channel

    @ui @no-api
    Scenario: Seeing price block without lowest price before the discount
        When I access the "Bizon Z056" product
        Then I should not see the lowest price before the discount in "United States" channel
