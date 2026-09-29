<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminDashboardService;


class AdminDashboardController extends Controller
{


    public function __construct(
        protected AdminDashboardService $dashboard
    ) {}

    public function index()
    {
        return view(
            'admin.dashboard.index',
            [
                'stats' =>
                    $this->dashboard
                        ->statistics()
            ]
        );
    }
}
