<?php

namespace Signifly\Cancellation\Test\Traits;

use Signifly\Cancellation\Test\Models\Order;

trait FindsOrders
{
    public function getCancelledOrder()
    {
        return Order::onlyCancelled()->get()->random();
    }

    public function getRegularOrder()
    {
        return Order::withoutCancelled()->get()->random();
    }
}
