<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\ScamReportsService;

class ScamReportsController extends Controller
{
    protected $scamReportsService;

    public function __construct(ScamReportsService $scamReportsService)
    {
        $this->scamReportsService = $scamReportsService;
    }

    public function postScamReport(Request $request)
    {
        $message = $this->scamReportsService->postScamReport($request);
        return $message;
    }


    public function searchScamReports(Request $request)
    {
        $message = $this->scamReportsService->searchScamReports($request);
        return $message;
    }
}
