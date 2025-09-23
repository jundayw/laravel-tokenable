<?php

namespace Jundayw\Tokenable\Models;

use Jundayw\Tokenable\Concerns\Auth\Authenticatable;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;

class Authentication implements Authenticable
{
    use Authenticatable;
}
