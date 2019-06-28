@viewing_products
Feature: Viewing a product for a particular channel
    In order to choosing view a product in each channel
    As an Administrator
    I want to be able to view product shop page for particular channel

    Background:
        Given the store operates on a channel named "UnitedStates" with hostname "goodcars.com"
        And the store also operates on a channel named "Europe" with hostname "goodcars.eu"
        And the store classifies its products as "Shield" and "Equipment"
        And the store has a product "Iron shield" priced at "$20.00" in "UnitedStates" channel
        And this product is available in "Europe" channel
        And I am logged in as an administrator
        And I am browsing products

    @ui
    Scenario: Viewing product shop page for particular channel
        When I access "Iron shield" product page
        And I specify this product to viewing in "Europe" channel
        Then I should see this product in "Europe" channel
