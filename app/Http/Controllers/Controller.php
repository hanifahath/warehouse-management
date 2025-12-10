<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // PENTING: Untuk otorisasi
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // PENTING: Untuk metode middleware()

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}