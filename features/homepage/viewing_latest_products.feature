@homepage
Feature: Viewing a latest product list
    In order to be up-to-date with the newest products
    As a Visitor
    I want to be able to view a latest product list

    Background:
        Given the store operates on a single channel in "United States"
        And this channel has "Belvedere Vodka", "Coconaut Liqeur", "Chopin Chocolate Liquer" and "Capitan Morgan White Rum" products

    @ui
    Scenario: Viewing latest products
        When I check latest products
        Then I should see 4 products in the list

    @ui
    Scenario: Viewing latest products with translation
        Given the product "Belvedere Vodka" is named "Wódka Belveder" in the "Polish (Poland)" locale
        And the product "Coconaut Liqeur" is named "Likier kokosowy" in the "Polish (Poland)" locale
        And the product "Chopin Chocolate Liquer" is named "Chopin Likier Czekoladowy" in the "Polish (Poland)" locale
        And the product "Capitan Morgan White Rum" is named "Capitan Morgan Biały Rum" in the "Polish (Poland)" locale
        When I check latest products
        Then I should see 4 products in the list
