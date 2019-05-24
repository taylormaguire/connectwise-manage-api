<?php
namespace taylormaguire\CWManageAPI;

use Illuminate\Support\Facades\Facade;

class CWManageAPIFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CWManageAPI';
    }
}
