<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\App;
use App\Container;
use App\Services\InvoiceService;
use App\View;

class HomeController
{

    public function __construct(private InvoiceService $invoiceService)
    {
    }

    public function index(): View
    {
        $this->invoiceService->process([], 2.3);


        return View::make('index');
    }
}
