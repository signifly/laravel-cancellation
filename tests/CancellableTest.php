<?php

namespace Signifly\Cancellation\Test;

use Illuminate\Support\Facades\Event;
use Signifly\Cancellation\Test\Models\Order;
use Signifly\Cancellation\Test\Traits\FindsOrders;

class CancellableTest extends TestCase
{
    use FindsOrders;

    /** @test */
    function it_can_check_if_the_order_is_cancelled()
    {
        $order = $this->getRegularOrder();

        $this->assertFalse($order->isCancelled());

        $order->cancel();

        $this->assertTrue($order->isCancelled());
        $this->assertTrue($order->fresh()->isCancelled());
        $this->assertNotNull($order->getCancelledAtColumn());
    }

    /** @test */
    function an_order_can_be_cancelled()
    {
        $order = $this->getRegularOrder();

        $this->assertFalse($order->isCancelled());

        $order->cancel();

        $this->assertTrue($order->isCancelled());
    }

    /** @test */
    function a_cancelled_order_can_be_kept()
    {
        $cancelledOrder = $this->getCancelledOrder();

        $this->assertTrue($cancelledOrder->isCancelled());

        $cancelledOrder->keep();

        $this->assertFalse($cancelledOrder->isCancelled());
    }

    /** @test */
    function cancelling_an_order_fires_expected_events()
    {
        Event::fake();

        $order = $this->getRegularOrder();
        $order->cancel();

        Event::assertDispatched('eloquent.cancelling: Signifly\Cancellation\Test\Models\Order');
        Event::assertDispatched('eloquent.cancelled: Signifly\Cancellation\Test\Models\Order');
    }

    /** @test */
    function keeping_an_order_fires_expected_events()
    {
        Event::fake();

        $cancelledOrder = $this->getCancelledOrder();

        $cancelledOrder->keep();

        Event::assertDispatched('eloquent.keeping: Signifly\Cancellation\Test\Models\Order');
        Event::assertDispatched('eloquent.kept: Signifly\Cancellation\Test\Models\Order');
    }

    /** @test */
    function cancelling_event_can_receive_a_callback()
    {
        $called = false;

        Order::cancelling(function () use (&$called) {
            $called = true;
        });

        $this->assertFalse($called);

        $order = $this->getRegularOrder();
        $order->cancel();

        $this->assertTrue($called);
    }

    /** @test */
    function cancelled_event_can_receive_a_callback()
    {
        $order = $this->getRegularOrder();
        $total = $order->total;

        Order::cancelled(function ($order) {
            $order->total = $order->total * 10;
        });

        $this->assertEquals($total, $order->total);

        $order->cancel();

        $this->assertEquals($total * 10, $order->total);
    }

    /** @test */
    function keeping_event_can_receive_a_callback()
    {
        $called = false;

        Order::keeping(function () use (&$called) {
            $called = true;
        });

        $this->assertFalse($called);

        $cancelledOrder = $this->getCancelledOrder();
        $cancelledOrder->keep();

        $this->assertTrue($called);
    }

    /** @test */
    function kept_event_can_receive_a_callback()
    {
        $called = false;

        Order::kept(function () use (&$called) {
            $called = true;
        });

        $this->assertFalse($called);

        $order = $this->getCancelledOrder();
        $order->keep();

        $this->assertTrue($called);
    }
}
