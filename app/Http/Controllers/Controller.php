<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Controller constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
