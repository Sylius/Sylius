@managing_products
Feature: Accessing a store's product page
    In order to access a store's product page from the admin panel
    As an Administrator
    I want to have a way to be redirected to the store's product page from the admin panel

    Background:
        Given the store operates on a single channel in "German (Germany)" locale
        And the store has a product "Französischer Bulldoggen T-Shirt"
        And I am logged in as an administrator
        And I am using "Polish (Poland)" locale for my panel

    @ui @no-api
    Scenario: Accessing the product shop page from the admin panel when the product has a translation with a defined slug in the administrator's chosen language
        Given the locale "Polish (Poland)" is enabled
        And this product is named "Bulldog francuski T-Shirt" in the "Polish (Poland)" locale
        When I want to edit this product
        Then the show product's page button should be enabled
        And it should be leading to the product's page in the "Polish (Poland)" locale

    @ui @no-api
    Scenario: Accessing the product shop page from the admin panel when the product has a translation with a defined slug in the default channel's language
        Given the locale "French (France)" is enabled
        And this product is named "Tee-shirt bouledogue français" in the "French (France)" locale
        When I want to edit this product
        Then the show product's page button should be enabled
        And it should be leading to the product's page in the "German (Germany)" locale

    @ui @no-api
    Scenario: Accessing the product shop page from the admin panel with using first available locale with slug and enabled in the channel
        Given the locale "French (France)" is enabled
        And the store also operates in "French (France)" locale
        And this product has no slug in the "German (Germany)" locale
        And this product is named "T-shirt bouledogue français" in the "French (France)" locale
        When I want to edit this product
        Then the show product's page button should be enabled
        And it should be leading to the product's page in the "French (France)" locale

    @ui @no-api
    Scenario: Not being able to access the product shop page from the admin panel when the product has no translations meeting the criteria
        Given this product has no translations with a defined slug
        When I want to edit this product
        Then the show product's page button should be disabled
