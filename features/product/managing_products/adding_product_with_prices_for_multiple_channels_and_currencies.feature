@managing_products
Feature: Adding product with prices for multiple channels
    In order to define products for each channel
    As an Administrator
    I want to add a new product to the shop with different prices for each channel

    Background:
        Given the store has currency "USD"
        And the store has currency "GBP" with exchange rate 0.7
        And the store operates on a channel named "Web-US"
        And that channel uses the "USD" currency by default
        And the store operates on another channel named "Web-GB"
        And that channel uses the "GBP" currency by default
        And I am logged in as an administrator

    @ui @javascript @todo
    Scenario: Configure prices for each channel and currency while adding new simple product
        When I want to create a new simple product
        And I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its default price to "$20.00"
        And I set its price to "$10.00" for channel "Web-US"
        And I set its price to "£5.00" for "Web-GB"
        And I set its slug to "dice-brewing"
        And I add it
        Then I should be notified that it has been successfully created
        And the product for "Web-US" channel should have "$10.00"
        And the product for "Web-US" channel should have "£10.00"
