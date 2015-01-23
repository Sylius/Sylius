@i18n
Feature: Products
    In order to create my offer
    As a store owner
    I want to be able to manage products

    Background:
        Given there is default currency configured
        And I am logged in as administrator
        And there are following locales configured:
            | code  | enabled |
            | en    | yes     |
            | es    | yes     |

    Scenario: Creating a product requires default translation fields
        Given I am on the product creation page
        And I fill in the following:
            | Price                                      | 29.99                    |
            | sylius_product_translations_es_name        | Soy i18n                 |
            | sylius_product_translations_es_description | Conmemorando el dia i18n |
        When I press "Create"
        Then I should still be on the product creation page
        And  I should see "Please enter product name."

    Scenario: Creating a product in specific locale
        Given I am on the product creation page
        And I fill in the following:
            | Price                                      | 29.99                    |
            | sylius_product_translations_es_name        | Soy i18n                 |
            | sylius_product_translations_es_description | Conmemorando el dia i18n |
            | sylius_product_translations_en_name        | I am i18n                 |
            | sylius_product_translations_en_description | Finally i18n |
        When I press "Create"
        Then "Product has been successfully created." should appear on the page
        And "es" translation for product "Soy i18n" should exist
        And "en" translation for product "I am i18n" should exist
