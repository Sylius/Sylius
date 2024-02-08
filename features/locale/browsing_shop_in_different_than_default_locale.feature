@locales
Feature: Browsing shop in different than default locale
    In order to fully understand the texts on the shop
    As a Customer
    I want to view texts in my preferred locale

    Background:
        Given the store operates on a channel named "Web" with hostname "web"
        And that channel allows to shop using "English (United States)", "Polish (Poland)" and "Chinese (Simplified, China)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "T-Shirt banana"
        And this product is named "Koszulka bananowa" in the "Polish (Poland)" locale
        And this product is named "香蕉T恤" in the "Chinese (Simplified, China)" locale

    @ui @api
    Scenario: Browsing product details in non-default locale
        When I browse that channel
        And I check this product's details in the "Polish (Poland)" locale
        Then I should see the product name "Koszulka bananowa"

    @ui @api
    Scenario: Browsing product details in non-default locale
        When I check this product's details in the "Chinese (Simplified, China)" locale
        Then I should see the product name "香蕉T恤"

    @ui @api
    Scenario: Not being able to shop using a locale non-existent in the channel
        When I browse that channel
        And I try to check this product's details in the "Irish (Ireland)" locale
        Then I should not be able to view this product in the "Irish (Ireland)" locale
