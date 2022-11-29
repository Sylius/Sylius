@channels
Feature: Filtering out disabled channels
    In order to avoid mistakes
    As a Customer
    I want to be able to browse only available shops

    Background:
        Given the store operates on a channel named "Fashion" in "USD" currency and with hostname "127.0.0.1"
        And the store operates on a channel named "Furniture" in "EUR" currency and with hostname "127.0.0.1"
        And there is product "Black T-Shirt" available in "Fashion" channel
        And there is product "Old Wardrobe" available in "Furniture" channel
        And the channel "Fashion" is disabled

    @ui @api
    Scenario: Seeing Furniture shop products
        When I browse the "Furniture" channel
        And I check latest products
        Then I should see "Old Wardrobe" product
        And I should not see "Black T-Shirt" product
