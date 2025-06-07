<?php
namespace App\Services;
use Illuminate\Http\Request;

use App\Models\ScamReport;
use Carbon\Carbon;
use DB;

use App\Models\ItemType;
use App\Models\SiteType;
use App\Models\BankType;
use Exception;

class ScamReportsService
{
    public function __construct()
    {
    }

    public function postScamReport(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:physical,virtual,private,crypto',
            'tradedItemCategory' => 'required|string',
            'tradedItemName' => 'nullable|string',
            'cryptoTypes' => 'nullable|string',
            'cryptoAddress' => 'nullable|string',
            'cryptoSentAmount' => 'nullable|numeric',
            'bankTypes' => 'nullable|string',
            'bankAccountOwnerName' => 'nullable|string',
            'bankAccountNumber' => 'nullable|string',
            'bankSentAmount' => 'nullable|numeric',
            'bankSentDate' => 'nullable|date',
            'scamPhoneNumber' => 'nullable|string',
            'suspectSex' => 'nullable|in:male,female,unknown',
            'suspectId' => 'nullable|string',
            'suspectDescription' => 'nullable|string',
            'victimName' => 'nullable|string',
            'websiteUrl' => 'nullable|string',
        ]);

        $itemType = ItemType::firstOrCreate(['name' => $data['tradedItemCategory']]);
        $siteType = SiteType::firstOrCreate(['name' => $data['websiteUrl']]);
        $bank = !empty($data['bankTypes']) ? BankType::firstOrCreate(['name' => $data['bankTypes']]) : null;

        $report = ScamReport::create([
            'report_type' => $data['type'],
            'item_type_id' => $itemType->id,
            'item_name' => $data['tradedItemName'] ?? '',
            'site_type_id' => $siteType->id,
            'crypto_type' => $data['cryptoTypes'] ?? null,
            'crypto_wallet_address' => $data['cryptoAddress'] ?? null,
            'crypto_amount' => $data['cryptoSentAmount'] ?? 0,
            'scammer_bank_id' => $bank?->id,
            'scammer_bank_account_name' => $data['bankAccountOwnerName'] ?? null,
            'scammer_bank_account_number' => $data['bankAccountNumber'] ?? null,
            'scammer_bank_amount' => $data['bankSentAmount'] ?? 0,
            'scammer_bank_sent_date' => !empty($data['bankSentDate']) ? Carbon::parse($data['bankSentDate']) : null,
            'scammer_phone_number' => $data['scamPhoneNumber'] ?? null,
            'scammer_sex' => $data['suspectSex'] ?? 'unknown',
            'scammer_id' => $data['suspectId'] ?? null,
            'description' => $data['suspectDescription'] ?? '',
            'victim_name' => $data['victimName'] ?? '',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Scam report created successfully.',
            'data' => $report
        ], 201);
    }
    

    public function searchScamReports(Request $request)
    {
        try {
            $searchType = $request->query('searchType');
            $query = $request->query('query');
    
            $sixMonthsAgo = Carbon::now()->subMonths(6);
    
            $queryBuilder = ScamReport::query();
    
            if ($searchType === 'accountNumber') {
                $queryBuilder->where('scammer_bank_account_number', $query);
            } elseif ($searchType === 'phoneNumber') {
                $queryBuilder->where('scammer_phone_number', $query);
            } elseif ($searchType === 'id') {
                $queryBuilder->where('scammer_id', $query);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid search type.'
                ], 400);
            }
    
            $scamReports = $queryBuilder
                ->where('created_at', '>=', $sixMonthsAgo)
                ->with('itemType:id,name')
                ->select('id', 'item_type_id', 'scammer_bank_amount', 'report_type', 'scammer_bank_sent_date')
                ->get();
    
            return response()->json([
                'success' => true,
                'data' => $scamReports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "Error",
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
