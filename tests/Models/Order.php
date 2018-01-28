<?php

namespace Signifly\Cancellation\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Signifly\Cancellation\Traits\Cancellable;

class Order extends Model
{
    use Cancellable;
}
