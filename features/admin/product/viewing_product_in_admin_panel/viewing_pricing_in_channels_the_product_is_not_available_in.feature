@viewing_product_in_admin_panel
Feature: Viewing pricing in channels the product is not available in
    In order to see all the prices defined for a product
    As an Administrator
    I want to see prices from channels the product is not available in as separate rows

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "GBP" currency
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Viewing pricing of a simple product priced in a channel it is not available in
        Given the store has a product "Dice Brewing" priced at "$10.00" in "Web-US" channel
        And this product is also priced at "£5.00" in "Web-GB" channel
        And this product is disabled in "Web-GB" channel
        And I am browsing products
        When I access the "Dice Brewing" product
        Then I should see price "$10.00" for channel "Web-US"
        And I should see a pricing row without a price for the "Web-GB" channel

    @no-api @ui
    Scenario: Viewing pricing of a product variant priced in a channel the product is not available in
        Given the store has a "Dice Brewing" configurable product
        And this product has "Dice Brewing - big" variant priced at "$10.00" in "Web-US" channel
        And this product has "Dice Brewing - small" variant priced at "$5.00" in "Web-US" channel
        And "Dice Brewing - big" variant priced at "£5.00" in "Web-GB" channel
        And this product is available in "Web-US" channel
        And this product is disabled in "Web-GB" channel
        And I am browsing products
        When I access the "Dice Brewing" product
        Then I should see a pricing row without a price for the "Dice Brewing - big" variant in the "Web-GB" channel
