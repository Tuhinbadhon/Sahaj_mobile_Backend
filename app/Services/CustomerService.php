<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CustomerService
{
    /**
     * Load customers from remote JSON source defined in config/customers.php
     * Returns a flat array of customer records or null on failure.
     */
    private function loadRemoteCustomers(): ?array
    {
        $url = config('customers.source_url');
        if (empty($url)) {
            return null;
        }

        $ttl = (int) config('customers.cache_ttl', 300);
        $cacheKey = 'customers.remote.'.md5($url);

        $fetch = function () use ($url) {
            try {
                $resp = Http::timeout(8)->get($url);
                if (!$resp->ok()) {
                    return null;
                }
                $json = $resp->json();

                if (!is_array($json)) {
                    return null;
                }

                // Handle wrapped response or flat array
                if (isset($json['data']) && is_array($json['data'])) {
                    return $json['data'];
                }

                if (isset($json[0]) && is_array($json[0])) {
                    return $json;
                }

                return null;
            } catch (\Throwable $e) {
                return null;
            }
        };

        if ($ttl > 0) {
            return Cache::remember($cacheKey, $ttl, $fetch) ?: null;
        }

        return $fetch();
    }

    public function getCustomers(
        int $page = 1,
        int $perPage = 50,
        ?int $shopId = null,
        string $status = 'all',
        string $search = '',
        string $sortBy = 'originate_date',
        string $sortOrder = 'desc'
    ): array {
        $allCustomers = $this->loadRemoteCustomers();
        if ($allCustomers === null) {
            throw new \RuntimeException('Remote customer source unavailable or invalid.');
        }

        // Filter by shop_id
        if ($shopId) {
            $allCustomers = array_filter($allCustomers, function($customer) use ($shopId) {
                return $customer['shop_id'] === $shopId;
            });
        }

        // Filter by status
        if ($status !== 'all') {
            $allCustomers = array_filter($allCustomers, function($customer) use ($status) {
                return strcasecmp($customer['status'], $status) === 0;
            });
        }

        // Filter by search (applicant or telephone)
        if (!empty($search)) {
            $allCustomers = array_filter($allCustomers, function($customer) use ($search) {
                return stripos($customer['applicant'], $search) !== false ||
                       stripos($customer['telephone'], $search) !== false;
            });
        }

        // Type-aware sorting
        usort($allCustomers, function($a, $b) use ($sortBy, $sortOrder) {
            $dateFields = ['originate_date', 'last_pay_date'];
            $numericFields = ['id', 'total_amount', 'due', 'installment', 'paid'];

            $aRaw = $a[$sortBy] ?? null;
            $bRaw = $b[$sortBy] ?? null;

            if (in_array($sortBy, $dateFields, true)) {
                $aVal = $aRaw ? strtotime((string) $aRaw) : 0;
                $bVal = $bRaw ? strtotime((string) $bRaw) : 0;
                $comparison = $aVal <=> $bVal;
            } elseif (in_array($sortBy, $numericFields, true)) {
                $aVal = is_numeric($aRaw) ? (float) $aRaw : 0.0;
                $bVal = is_numeric($bRaw) ? (float) $bRaw : 0.0;
                $comparison = $aVal <=> $bVal;
            } else {
                $aVal = strtolower((string) ($aRaw ?? ''));
                $bVal = strtolower((string) ($bRaw ?? ''));
                $comparison = strcmp($aVal, $bVal);
            }

            return $sortOrder === 'desc' ? -$comparison : $comparison;
        });

        $totalRecords = count($allCustomers);
        $totalPages = ceil($totalRecords / $perPage);

        $offset = ($page - 1) * $perPage;
        $paginatedCustomers = array_slice($allCustomers, $offset, $perPage);
        $paginatedCustomers = array_values($paginatedCustomers);

        return [
            'response' => 'success',
            'status' => 1,
            'message' => 'Data retrieved successfully',
            'pagination' => [
                'total_records' => $totalRecords,
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
            ],
            'data' => $paginatedCustomers,
        ];
    }
}
