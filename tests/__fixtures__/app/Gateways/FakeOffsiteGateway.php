<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Fixtures\Gateways;

use DoubleThreeDigital\SimpleCommerce\Contracts\Order as ContractsOrder;
use DoubleThreeDigital\SimpleCommerce\Gateways\BaseGateway;
use Illuminate\Http\Request;

class FakeOffsiteGateway extends BaseGateway
{
    public function name(): string
    {
        return 'Fake Offsite Gateway';
    }

    public function isOffsiteGateway(): bool
    {
        return true;
    }

    public function prepare(Request $request, ContractsOrder $order): array
    {
        return [];
    }

    public function refund(ContractsOrder $order): ?array
    {
        return [];
    }
}
