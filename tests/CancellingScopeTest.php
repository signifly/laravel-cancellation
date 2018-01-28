<?php

namespace Signifly\Cancellation\Test;

use Signifly\Cancellation\Test\Models\Order;

class CancellingScopeTest extends TestCase
{
    /** @test */
    function it_includes_cancelled_orders_by_default()
    {
        $this->assertCount(50, Order::all());
    }

    /** @test */
    function it_can_be_configured_to_exclude_cancelled_orders_by_default()
    {
        $this->app['config']->set('laravel-cancellation.exclude', true);

        $this->assertCount(25, Order::all());
    }

    /** @test */
    function it_can_include_cancelled_orders()
    {
        $this->assertCount(50, Order::withCancelled()->get());
    }

    /** @test */
    function it_can_only_receive_cancelled_orders()
    {
        $this->assertCount(25, Order::onlyCancelled()->get());
    }

    /** @test */
    function it_can_exclude_cancelled_orders()
    {
        $this->assertCount(25, Order::withoutCancelled()->get());
    }

    /** @test */
    function it_can_keep_multiple_cancelled_orders()
    {
        $this->assertCount(25, Order::withoutCancelled()->get());

        Order::onlyCancelled()->keep();

        $this->assertCount(50, Order::withoutCancelled()->get());
    }
}
