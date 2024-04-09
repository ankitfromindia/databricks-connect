<?php

namespace Ankitfromindia\StarbustQuery;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ankitfromindia\StarbustQuery\Skeleton\SkeletonClass
 */
class StarbustQueryFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'starbust-query';
    }
}
