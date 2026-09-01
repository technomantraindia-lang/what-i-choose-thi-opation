<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;
    protected ExportService $exportService;

    public function __construct(ReportService $reportService, ExportService $exportService)
    {
        $this->reportService = $reportService;
        $this->exportService = $exportService;
    }

    public function index(Request $request)
    {
        $preset = $request->get('date_preset', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $range = $this->reportService->resolveDateRange($preset, $dateFrom, $dateTo);
        $overview = $this->reportService->getOverviewReport($range);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'range' => $range,
                'data' => $overview,
            ]);
        }

        return view('admin.reports.index', [
            'range' => $range,
            'stats' => $overview,
        ]);
    }

    public function profit(Request $request)
    {
        $preset = $request->get('date_preset', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $range = $this->reportService->resolveDateRange($preset, $dateFrom, $dateTo);
        $profitData = $this->reportService->getProfitReport($range);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'range' => $range,
                'data' => $profitData,
            ]);
        }

        return view('admin.reports.profit', [
            'range' => $range,
            'profit' => $profitData,
        ]);
    }

    public function gst(Request $request)
    {
        $preset = $request->get('date_preset', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $hsn = $request->get('hsn');

        $range = $this->reportService->resolveDateRange($preset, $dateFrom, $dateTo);
        $gstData = $this->reportService->getGstReport($range, $hsn);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'range' => $range,
                'data' => $gstData,
            ]);
        }

        return view('admin.reports.gst', [
            'range' => $range,
            'gst' => $gstData,
        ]);
    }

    public function export(Request $request, string $module)
    {
        $preset = $request->get('date_preset', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $range = $this->reportService->resolveDateRange($preset, $dateFrom, $dateTo);
        $filters = $request->except(['date_preset', 'date_from', 'date_to']);

        return $this->exportService->exportCsv($module, $range, $filters);
    }
}
