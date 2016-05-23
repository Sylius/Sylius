@legacy @i18n
Feature: Products
    In order to create my offer
    As a store owner
    I want to be able to manage products

    Background:
        Given store has default configuration
        And there are following locales configured and assigned to the default channel:
            | code  |
            | en_US |
            | es_ES |
        And I am logged in as administrator

    Scenario: Creating a product requires default translation fields
        Given I am on the product creation page
        And I fill in the following:
            | sylius_product_legacy_translations_es_ES_name        | Soy i18n                 |
            | sylius_product_legacy_translations_es_ES_description | Conmemorando el dia i18n |
        When I press "Create"
        Then I should still be on the product creation page
        And I should see "Please enter product name"

    Scenario: Creating a product in specific locale
        Given I am on the product creation page
        And I fill in the following:
            | sylius_product_legacy_code                           | SOY_PRODUCT              |
            | sylius_product_legacy_translations_es_ES_name        | Soy i18n                 |
            | sylius_product_legacy_translations_es_ES_description | Conmemorando el dia i18n |
            | sylius_product_legacy_translations_en_US_name        | I am i18n                |
            | sylius_product_legacy_translations_en_US_description | Finally i18n             |
        When I press "Create"
        Then "Product has been successfully created" should appear on the page
        And "es_ES" translation for product "Soy i18n" should exist
        And "en_US" translation for product "I am i18n" should exist
