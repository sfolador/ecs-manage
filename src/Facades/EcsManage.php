<?php

namespace Sfolador\EcsManage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sfolador\EcsManage\EcsManage
 */
class EcsManage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sfolador\EcsManage\EcsManage::class;
    }
}
