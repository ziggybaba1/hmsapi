<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\Response;
use App\Traits\ReferenceTrait;
use App\Traits\LoggedTrait;

class Controller extends BaseController
{
    use Response,ReferenceTrait,LoggedTrait;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
