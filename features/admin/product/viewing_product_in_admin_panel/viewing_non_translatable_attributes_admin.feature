@viewing_product_in_admin_panel
Feature: Viewing product's non translatable attributes
    In order to see product's non translatable attribute
    As an Administrator
    I want to be able to see product's when checking details

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a product "Iron Pickaxe"
        And this product has non-translatable percent attribute "crit chance" with value 10%
        And this product has a text attribute "Material" with value "Iron" in "English (United States)" locale
        And this product has a text attribute "Material" with value "Żelazo" in "Polish (Poland)" locale
        And I am logged in as an administrator
        And I am browsing products

    @ui @api
    Scenario: Viewing product's non translatable attributes along with default ones
        When I access the "Iron Pickaxe" product
        Then I should see non-translatable attribute "crit chance" with value 10%
        And I should see attribute "Material" with value "Iron" in "English (United States)" locale
        And I should see attribute "Material" with value "Żelazo" in "Polish (Poland)" locale
