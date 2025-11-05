<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

   
    public function getCustomersList(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'shop_id' => 'nullable|integer',
            'status' => 'nullable|string',
            'search' => 'nullable|string',
            'sort_by' => 'nullable|in:originate_date,applicant,total_amount,id',
            'sort_order' => 'nullable|in:asc,desc'
        ]);

        try {
            $result = $this->customerService->getCustomers(
                $validated['page'] ?? 1,
                $validated['per_page'] ?? 50,
                $validated['shop_id'] ?? null,
                $validated['status'] ?? 'all',
                $validated['search'] ?? '',
                $validated['sort_by'] ?? 'originate_date',
                $validated['sort_order'] ?? 'desc'
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'response' => 'error',
                'status' => 0,
                'message' => 'Failed to retrieve data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

   
    public function exportCustomersCsv(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'shop_id' => 'nullable|integer',
            'status' => 'nullable|string',
            'search' => 'nullable|string',
            'sort_by' => 'nullable|in:originate_date,applicant,total_amount,id',
            'sort_order' => 'nullable|in:asc,desc'
        ]);

        $shopId = $validated['shop_id'] ?? null;
        $status = $validated['status'] ?? 'all';
        $search = $validated['search'] ?? '';
        $sortBy = $validated['sort_by'] ?? 'originate_date';
        $sortOrder = $validated['sort_order'] ?? 'desc';

        try {
            $result = $this->customerService->getCustomers(
                page: 1,
                perPage: 100000,
                shopId: $shopId,
                status: $status,
                search: $search,
                sortBy: $sortBy,
                sortOrder: $sortOrder
            );

            $rows = $result['data'] ?? [];

            $headers = [
                'Content-Type' => 'text/csv',
            ];

            $filename = 'customers_'.now()->format('Ymd_His').'.csv';

            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                // CSV header
                fputcsv($out, [
                    'ID',
                    'Originate Date',
                    'Applicant',
                    'Telephone',
                    'Shop Name',
                    'EMI Package',
                    'Months/Weeks',
                    'Installment',
                    'Total Amount',
                    'Paid',
                    'Due',
                    'Last Pay Date',
                    'Status',
                ]);

                foreach ($rows as $c) {
                    fputcsv($out, [
                        $c['id'] ?? '',
                        $c['originate_date'] ?? '',
                        $c['applicant'] ?? '',
                        $c['telephone'] ?? '',
                        $c['shop_name'] ?? '',
                        $c['emi_package'] ?? '',
                        $c['month_week'] ?? '',
                        isset($c['installment']) ? (float) $c['installment'] : '',
                        isset($c['total_amount']) ? (float) $c['total_amount'] : '',
                        isset($c['paid']) ? (float) $c['paid'] : '',
                        isset($c['due']) ? (float) $c['due'] : '',
                        $c['last_pay_date'] ?? '',
                        $c['status'] ?? '',
                    ]);
                }

                fclose($out);
            }, $filename, $headers);
        } catch (\Throwable $e) {
            $headers = [ 'Content-Type' => 'text/csv' ];
            $filename = 'customers_error_'.now()->format('Ymd_His').'.csv';
            return response()->streamDownload(function () use ($e) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['error']);
                fputcsv($out, [substr($e->getMessage(), 0, 200)]);
                fclose($out);
            }, $filename, $headers);
        }
    }
}
