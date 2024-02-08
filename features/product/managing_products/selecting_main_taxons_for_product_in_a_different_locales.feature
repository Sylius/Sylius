@managing_products
Feature: Selecting main taxon for product in different locales
    In order to be consistent with the store's chosen locale
    As an Administrator
    I want to be able to choose only taxons from the current locale

    Background:
        Given the store operates on a channel named "Web"
        And the store has many locales
        And the store has "Category" taxonomy
        And the "Category" taxon has child taxon "T-Shirts" in many locales
        And the "T-Shirts" taxon has child taxon "Men-T-Shirts" in many locales
        And the "T-Shirts" taxon has child taxon "Woman-T-Shirts" in many locales
        And the "Category" taxon has child taxon "Jeans" in many locales
        And the "Jeans" taxon has child taxon "Men-Jeans" in many locales
        And the "Jeans" taxon has child taxon "Woman-Jeans" in many locales
        And the store has a product "T-Shirt Batman"
        And I am logged in as an administrator

    @ui @javascript @no-api
    Scenario: Choosing only taxons from the Polish locale
        Given I am using "Polish (Poland)" locale for my panel
        When I want to choose main taxon for product "T-Shirt Batman"
        Then I should be able to choose taxon "Men-T-Shirts_PL" from the list
        And I should be able to choose taxon "Woman-T-Shirts_PL" from the list
        And I should not be able to choose taxon "Woman-T-Shirts_UA" from the list

    @ui @javascript @no-api
    Scenario: Choosing only taxons from the French locale
        Given I am using "French (France)" locale for my panel
        When I want to choose main taxon for product "T-Shirt Batman"
        Then I should be able to choose taxon "Men-T-Shirts_FR" from the list
        And I should be able to choose taxon "Woman-T-Shirts_FR" from the list
        And I should not be able to choose taxon "Woman-T-Shirts_UA" from the list

    @ui @javascript @no-api
    Scenario: Choosing only taxons from the German locale
        Given I am using "German (Germany)" locale for my panel
        When I want to choose main taxon for product "T-Shirt Batman"
        Then I should be able to choose taxon "Men-T-Shirts_DE" from the list
        And I should be able to choose taxon "Woman-T-Shirts_DE" from the list
        And I should not be able to choose taxon "Woman-T-Shirts_UA" from the list
