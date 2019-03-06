<?php

namespace Signifly\Cancellation\Test;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase as Orchestra;
use Signifly\Cancellation\CancellationServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('app.key', 'base64:9e0yNQB60wgU/cqbP09uphPo3aglW3iQJy+u4JQgnQE=');
    }

    protected function getPackageProviders($app)
    {
        return [
            CancellationServiceProvider::class,
        ];
    }

    protected function setUpDatabase()
    {
        $this->createOrdersTable();
        $this->seedOrdersTable();
    }

    protected function createOrdersTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('total');
            $table->cancellable();
            $table->timestamps();
        });
    }

    protected function seedOrdersTable()
    {
        for ($i = 0; $i < 50; $i++) {
            $time = null;
            if ($i % 2 == 0) {
                $time = Carbon::now();
            }
            DB::table('orders')->insert([
                'total'        => rand(100, 5000),
                'cancelled_at' => $time,
            ]);
        }
    }

    protected function expectsEvent($eventClassName)
    {
        Event::listen($eventClassName, function ($event) use ($eventClassName) {
            $this->firedEvents[] = $eventClassName;
        });
        $this->beforeApplicationDestroyed(function () use ($eventClassName) {
            $firedEvents = isset($this->firedEvents) ? $this->firedEvents : [];
            if (!in_array($eventClassName, $firedEvents)) {
                throw new Exception("Event {$eventClassName} not fired");
            }
        });
    }
}
