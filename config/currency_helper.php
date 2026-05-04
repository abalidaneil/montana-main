<?php
// Country to Currency Mapping
$country_currency_map = [
    'United States' => 'USD',
    'United Kingdom' => 'GBP',
    'Canada' => 'CAD',
    'Australia' => 'AUD',
    'New Zealand' => 'NZD',
    'European Union' => 'EUR',
    'Germany' => 'EUR',
    'France' => 'EUR',
    'Italy' => 'EUR',
    'Spain' => 'EUR',
    'Netherlands' => 'EUR',
    'Belgium' => 'EUR',
    'Austria' => 'EUR',
    'Japan' => 'JPY',
    'China' => 'CNY',
    'India' => 'INR',
    'Brazil' => 'BRL',
    'Mexico' => 'MXN',
    'Singapore' => 'SGD',
    'Hong Kong' => 'HKD',
    'Switzerland' => 'CHF',
    'Sweden' => 'SEK',
    'Norway' => 'NOK',
    'Denmark' => 'DKK',
    'South Korea' => 'KRW',
    'Russia' => 'RUB',
    'Turkey' => 'TRY',
    'South Africa' => 'ZAR',
    'Nigeria' => 'NGN',
    'Kenya' => 'KES',
    'Egypt' => 'EGP',
    'Saudi Arabia' => 'SAR',
    'United Arab Emirates' => 'AED',
    'Israel' => 'ILS',
    'Thailand' => 'THB',
    'Malaysia' => 'MYR',
    'Philippines' => 'PHP',
    'Indonesia' => 'IDR',
    'Pakistan' => 'PKR',
    'Bangladesh' => 'BDT',
    'Vietnam' => 'VND',
    'Poland' => 'PLN',
    'Czech Republic' => 'CZK',
    'Hungary' => 'HUF',
    'Greece' => 'EUR',
    'Portugal' => 'EUR',
    'Ireland' => 'EUR',
    'Chile' => 'CLP',
    'Argentina' => 'ARS',
    'Colombia' => 'COP',
    'Peru' => 'PEN',
    'Ukraine' => 'UAH',
];

// Supported Currency Symbols
$currency_symbols = [
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'JPY' => '¥',
    'CNY' => '¥',
    'INR' => '₹',
    'BRL' => 'R$',
    'CAD' => 'C$',
    'AUD' => 'A$',
    'CHF' => 'CHF',
    'SEK' => 'kr',
    'NOK' => 'kr',
    'DKK' => 'kr',
    'KRW' => '₩',
    'THB' => '฿',
    'MYR' => 'RM',
    'PHP' => '₱',
    'IDR' => 'Rp',
    'SGD' => 'S$',
    'HKD' => 'HK$',
    'NZD' => 'NZ$',
    'ZAR' => 'R',
    'TRY' => '₺',
    'RUB' => '₽',
    'NGN' => '₦',
    'KES' => 'KSh',
    'EGP' => 'E£',
    'SAR' => 'SR',
    'AED' => 'AED',
    'ILS' => '₪',
    'PKR' => '₨',
    'BDT' => '৳',
    'VND' => '₫',
    'PLN' => 'zł',
    'CZK' => 'Kč',
    'HUF' => 'Ft',
    'UAH' => '₴',
    'MXN' => '$',
    'CLP' => '$',
    'ARS' => '$',
    'COP' => '$',
    'PEN' => 'S/.',
];

// Get currency symbol for a given currency code
function getCurrencySymbol($currency_code) {
    global $currency_symbols;
    return isset($currency_symbols[$currency_code]) ? $currency_symbols[$currency_code] : $currency_code;
}

// Get default currency for a country
function getCurrencyByCountry($country) {
    global $country_currency_map;
    return isset($country_currency_map[$country]) ? $country_currency_map[$country] : 'USD';
}

// Get all available currencies
function getAllCurrencies() {
    global $currency_symbols;
    return array_keys($currency_symbols);
}

// Format currency with symbol
function formatCurrency($amount, $currency_code) {
    $symbol = getCurrencySymbol($currency_code);
    return $symbol . number_format($amount, 2);
}

// Format currency inline for display (used in tables)
function formatCurrencyInline($amount, $currency_code) {
    $symbol = getCurrencySymbol($currency_code);
    return $symbol . ' ' . number_format($amount, 2);
}
?>
