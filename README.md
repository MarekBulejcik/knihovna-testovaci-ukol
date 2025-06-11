# Katalog e-knih - Zkušební úkol

Malá webová aplikace pro katalogizaci e-knih vytvořená jako zkušební úkol. Aplikace se skládá z veřejné části pro prohlížení a z administrace pro správu knih.

Vypracováno s assistencí AI a dalšími internetovými zdroji.\*

## Použité technologie

- **Backend:** PHP 8.x (čisté PHP, objektově orientovaný přístup)
- **Databáze:** SQLite
- **Frontend:** HTML, SASS/CSS, Vanilla JavaScript

## Funkce aplikace

### Veřejná část

- Výpis všech knih v katalogu.
- Zobrazení detailu knihy po kliknutí na její název.
- Možnost tisku výpisu knih i detailu knihy (verze pro tisk je optimalizovaná a skrývá nepotřebné prvky).

### Administrační část

- Jednoduché přihlášení pomocí hesla.
- Přidání nové knihy do databáze pomocí formuláře s validací na straně serveru.
- Možnost importu knih z JSON souboru nahraného z počítače uživatele.

## Návod k instalaci

1.  **Naklonujte repozitář:**
    ```bash
    git clone [https://github.com/MarekBulejcik/knihovna-testovaci-ukol.git](https://github.com/MarekBulejcik/knihovna-testovaci-ukol.git)
    ```
2.  **Přesuňte adresář:**
    Přesuňte celý adresář `knihovna-testovaci-ukol` do složky `htdocs` vaší lokální serverové aplikace (např. XAMPP).
    _Cílová cesta bude například `C:/xampp/htdocs/knihovna-testovaci-ukol/`._

3.  **Práva pro zápis:**
    Ujistěte se, že má webový server (PHP) oprávnění zapisovat do kořenového adresáře projektu. Je to potřeba pro automatické vytvoření souboru s databází `database.sqlite`.

4.  **Spuštění:**
    Otevřete v prohlížeči adresu: `http://localhost/knihovna-testovaci-ukol/public/`

## Konfigurace přihlašovacích údajů

[cite_start]Přihlašovací údaje do administrace se nastavují v souboru `config.php`.

- [cite_start]**Heslo do administrace:** Otevřete soubor `config.php` a změňte hodnotu konstanty `ADMIN_PASSWORD`.

  ```php
  // Příklad změny hesla
  define('ADMIN_PASSWORD', 'MojeNoveBezpecneHeslo456');
  ```

- **URL administrace:** `http://localhost/knihovna-testovaci-ukol/public/admin.php`
