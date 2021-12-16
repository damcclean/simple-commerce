<?php

namespace DoubleThreeDigital\SimpleCommerce\Support;

use DoubleThreeDigital\SimpleCommerce\Contracts\Currency as Contract;
use DoubleThreeDigital\SimpleCommerce\Exceptions\CurrencyFormatterNotWorking;
use Illuminate\Support\Facades\Config;
use Money\Currencies\ISOCurrencies;
use Money\Currency as MoneyCurrency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;
use Statamic\Sites\Site;

class Currency implements Contract
{
    public function get(Site $site): array
    {
        $siteSettings = collect(Config::get('simple-commerce.sites'))
            ->get($site->handle());

        return Currencies::where('code', $siteSettings['currency'])
            ->first();
    }

    public function parse($amount, Site $site): string
    {
        if (is_string($amount)) {
            if (str_contains($amount, '.')) {
                $amount = str_replace('.', '', $amount);
            }

            $amount = (int) $amount;
        }

        if (is_float($amount)) {
            $amount = $amount * 100;
        }

        try {
            $money = new Money(str_replace('.', '', (int) $amount), new MoneyCurrency($this->get($site)['code']));

            $numberFormatter = new NumberFormatter($site->locale(), \NumberFormatter::CURRENCY);
            $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

            return $moneyFormatter->format($money);
        } catch (\ErrorException $e) {
            throw new CurrencyFormatterNotWorking('Extension PHP-intl not installed.');
        }
    }

    public function toPence(float $amount): int
    {
        return $amount * 100;
    }

    public function toDecimal(int $amount): float
    {
        return $amount / 100;
    }

    public static function bindings(): array
    {
        return [];
    }
}
