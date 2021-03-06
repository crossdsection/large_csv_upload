<?php

namespace App\Http\Controllers;

use App\Models\Tempname1db;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Tempname1dbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $qbDB = Tempname1db::query();
        $limit = $request->input('limit');
        if ($request->has('sortby')) {
            $qbDB->orderBy($request->input('sortby'));
        }
        if ($request->has('country')) {
            $qbDB->where('country', $request->input('country'));
        }
        if ($request->has('fromYear')) {
            $qbDB->where('year', '>=', $request->input('fromYear'));
        }
        if ($request->has('toYear')) {
            $qbDB->where('year', '<=', $request->input('toYear'));
        }
        if ($request->has('search') && strlen($request->input('search')) > 0 ) {
            $qbDB->where('country', 'like', '%'.$request->input('search').'%');
        }
        $totalCount = $qbDB->count();        
        $numberOfPages = ceil($totalCount/$limit);
        if ($request->has('offset')) {
            $qbDB->offset($request->input('offset'));
        }
        if ($request->has('limit')) {
            $qbDB->limit($limit);
        }
        $result = $qbDB->get();
        return array("error" => 0, "data" => $result, 'numberOfPages' => $numberOfPages, 'totalRows' => $totalCount );
    }

    public function getPercentagePerYear() {
        $data = DB::table('sale_percentage_per_year')->get();
        return array("error" => 0, 'data' => $data);
    }
}
