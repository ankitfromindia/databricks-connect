<?php

namespace Ankitfromindia\DatabricksConnect;

use Illuminate\Support\Facades\Facade;

class DatabricksFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'databricks';
    }
}
