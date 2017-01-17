@locales
Feature: Browsing shop in different than default locale
    In order to fully understand the texts on the shop
    As a Customer
    I want to view texts in my preferred locale

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "T-shirt banana"
        And this product is named "Koszulka bananowa" in the "Polish (Poland)" locale

    @ui
    Scenario: Switching the current locale
        When I browse that channel
        And I switch to the "Polish (Poland)" locale
        And I check this product's details
        Then I should see the product name "Koszulka bananowa"
