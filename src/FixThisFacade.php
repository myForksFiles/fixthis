<?php
namespace MyForksFiles\FixThis;

use Illuminate\Support\Facades\Facade;

class FixThisFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fixthis';
    }
}