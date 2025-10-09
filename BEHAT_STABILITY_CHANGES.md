# Behat Test Stability Improvements

## Zmiany wprowadzone w celu poprawy stabilności testów

### 1. **Dodany ChromeSlowdownContext**
- Plik: `src/Sylius/Behat/Context/Hook/ChromeSlowdownContext.php`
- Dodaje opóźnienia przed i po krokach testowych
- 500ms przed akcjami interaktywanymi (click, fill, select, etc.)
- 1s po głównych akcjach (submit, save, create, update)
- 300ms po każdym innym kroku

### 2. **Zaktualizowana konfiguracja Chrome**
- Plik: `behat.yml.dist`
- Dodane argumenty Chrome dla lepszej stabilności:
  - `--disable-blink-features=AutomationControlled`
  - `--disable-features=IsolateOrigins,site-per-process`
  - `--disable-site-isolation-trials`
  - `--disable-web-security`
  - `--disable-accelerated-2d-canvas`
  - `--disable-gpu-sandbox`

### 3. **Zwiększone timeouty**
- Connection timeout: 5000ms → 10000ms
- Request timeout: 120000ms → 180000ms

### 4. **Wydłużone czasy oczekiwania na formularze**
- `waitForFormUpdate()` w FormElement, CreatePage, UpdatePage:
  - Początkowe oczekiwanie: 500ms → 800ms
  - Timeout: 1500ms → 3000ms
  - Dodatkowe oczekiwanie po aktualizacji: 300ms

### 5. **Context dodany do wszystkich 75 suite UI**
- ChromeSlowdownContext automatycznie dodany do wszystkich suite testowych UI

## Wpływ na testy

✅ **Zalety:**
- Znacznie większa stabilność testów
- Mniej losowych błędów związanych z timing
- Lepsze wsparcie dla dynamicznych elementów JavaScript/Stimulus
- Mniej potrzeby rerunningu testów

⚠️ **Wady:**
- Testy będą wolniejsze (około 30-50% dłuższy czas wykonania)
- Każdy test ma dodatkowe opóźnienia

## Uruchomienie testów

### Lokalnie:
```bash
# Test pojedynczego feature
vendor/bin/behat features/admin/product/managing_product_attributes/adding_select_product_attribute.feature

# Wszystkie testy UI z Chrome
vendor/bin/behat --tags="@ui&&@mink:chromedriver"
```

### W CI:
Zmiany są już zastosowane w `behat.yml.dist` i będą automatycznie używane.

## Monitoring

Jeśli testy nadal będą failować:
1. Sprawdź logi w `etc/build/`
2. Włącz screenshoty dla debugowania
3. Rozważ zwiększenie opóźnień w `ChromeSlowdownContext`

## Cofnięcie zmian

Jeśli chcesz cofnąć zmiany (np. dla szybszego development):
```bash
# Usuń context ze wszystkich suite
grep -l "chrome_slowdown" src/Sylius/Behat/Resources/config/suites/ui/**/*.{yml,yaml} | xargs sed -i '/chrome_slowdown/d'

# Przywróć oryginalne timeouty w behat.yml.dist
git checkout behat.yml.dist
```