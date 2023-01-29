<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
      $subQuery = Order::betweenDate($request->startDate, $request->endDate);

      if($request->type === 'perDay')
      {
        $subQuery->where('status', true)
        ->groupBy('id')
        ->selectRaw("id, sum(subtotal) as totalPerPurchase,
        DATE_FORMAT(created_at, '%Y%m%d') as date");

        $date = DB::table($subQuery)
        ->groupBy('date')
        ->selectRaw('date, sum(totalPerPurchase) as total')
        ->get();

        $labels = $date->pluck('date');
        $totals = $date->pluck('total');
      }


      return response()->json([
        "data" => $date,
        "type" => $request->type,
        "labels" => $labels,
        "totals" => $totals,
      ], Response::HTTP_OK);
    }
}
