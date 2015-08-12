@seo
Feature: Page metadata
  In order to optimize my page for search engines
  As a store owner
  I want to manage and show page metadata

  Scenario: Image data association and internationalization
     When I search for that image metadata using Polish locale
     Then I should get following metadata:
      | Alternative text | Title                     |
      | Dojrzałe banany  | Najlepsze banany na rynku |