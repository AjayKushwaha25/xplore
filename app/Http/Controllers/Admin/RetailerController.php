<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Retailer,LoginHistory};
use Illuminate\Http\Request;
use DataTables;
use App\Actions\CalculatePendingPayoutAction;

class RetailerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            // 'counts' => $this->getCountsForSideNav(),
            // 'retailers' => Retailer::get(),
        ];
        return view('admin.view.retailers', compact('data'));
    }

    public function retailersList(Request $request){

        $query = Retailer::query();
        $filterByDate = $request->get('filter-by-date');
        $today = now()->startOfDay();

        switch ($filterByDate) {
            case 'today':
                $retailers = $query->where('created_at', '>=', $today);
                break;
            case 'last7days':
                $retailers = $query->where('created_at', '>=', $today->subDays(7));
                break;
            case 'last30days':
                $retailers = $query->where('created_at', '>=', $today->subDays(30));
                break;
            case 'last90days':
                $retailers = $query->where('created_at', '>=', $today->subDays(90));
                break;
            default:
                $retailers = $query;
                break;
        }

        // $retailers = Retailer::query();

        return Datatables::of($retailers)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Retailer  $retailer
     * @return \Illuminate\Http\Response
     */
    public function show(Retailer $retailer, CalculatePendingPayoutAction $calculatePendingPayoutAction)
    {
        $data = $calculatePendingPayoutAction->handle($retailer->id,true);

        return view('admin.view-by-id.retailer', compact('retailer','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Retailer  $retailer
     * @return \Illuminate\Http\Response
     */
    public function edit(Retailer $retailer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Retailer  $retailer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Retailer $retailer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Retailer  $retailer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Retailer $retailer)
    {
        //
    }


    /* Extra */
    public function getRetailerCount(Request $request)
    {
        $query = Retailer::query();
        $range = $request->get('range');
        $today = now()->startOfDay();

        switch ($range) {
            case 'today':
                $count = $query->where('created_at', '>=', $today)->count();
                break;
            case 'last7days':
                $count = $query->where('created_at', '>=', $today->subDays(7))->count();
                break;
            case 'last30days':
                $count = $query->where('created_at', '>=', $today->subDays(30))->count();
                break;
            case 'last90days':
                $count = $query->where('created_at', '>=', $today->subDays(90))->count();
                break;
            default:
                $count = $query->count();
                break;
        }

        return response()->json(['count' => $count]);
    }
}
