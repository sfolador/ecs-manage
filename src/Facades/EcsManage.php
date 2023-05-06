<?php

namespace Sfolador\EcsManage\Facades;

use Illuminate\Support\Facades\Facade;
use Sfolador\EcsManage\EcsManageInterface;

/**
 * @see \Sfolador\EcsManage\EcsManage
 */
class EcsManage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EcsManageInterface::class;
    }
}
