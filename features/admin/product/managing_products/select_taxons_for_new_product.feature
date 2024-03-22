@managing_products
Feature: Select taxon for a new product
    In order to specify in which taxons a product is available
    As an Administrator
    I want to be able to select taxons for a new product

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts", "Accessories", "Funny" and "Sad"
        And I am logged in as an administrator
        And I am using "English (United Kingdom)" locale for my panel

    @ui @javascript @api
    Scenario: Specifying main taxon for configurable product
        When I want to create a new configurable product
        And I choose main taxon "Sad"
        And I name it "Gentleman Jack" in "English (United States)"
        And I specify its code as "WHISKEY_GENTLEMEN"
        And I set its slug to "whiskey/gentleman-jack" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And main taxon of product "Gentleman Jack" should be "Sad"

    @ui @javascript @no-api
    Scenario: Specifying main taxon for simple product
        When I want to create a new simple product
        And I choose main taxon "Sad"
        And I name it "Mansion of Madness" in "English (United States)"
        And I specify its code as "BOARD_MANSION_OF_MADNESS"
        And I set its price to "$100.00" for "United States" channel
        And I set its slug to "mom-board-game" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And main taxon of product "Mansion of Madness" should be "Sad"
