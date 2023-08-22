<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ExportRequest;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class RazorpayController extends Controller
{
    public function index()
    {
        return view('admin.razorpay.index');
    }

    public function exportData(ExportRequest $request){
        ini_set("memory_limit", "-1");
        set_time_limit(0);

        $table = $request->validated('table');

        $startDate = Carbon::parse($request->validated('start_date'))->format('Y-m-d 00:00:00');
        $endDate = Carbon::parse($request->validated('end_date'))->format('Y-m-d 23:59:59');

        $fileName = config('constants.export_filename_prefix').$table.'-'.now()->format('Y-m-d-h:i:sa').".csv";
        $exportTableName = Str::singular(Str::ucfirst(Str::camel($table)));
        $exportClassName = "App\Exports\\".$exportTableName."Export";

        // dd($exportClassName);

        if(class_exists($exportClassName)===true){
            $classInstance = new $exportClassName;
            $classInstance->dateRange($startDate,$endDate);
            return Excel::download($classInstance, $fileName, \Maatwebsite\Excel\Excel::CSV);
        }else{
            $table = ucwords(str_replace('_', ' ', Str::singular($table)));
            return back()->withErrors(['classNotFoundError' => "The export for {$table} table does not exist please try again later or contact administrator."])->withInput($request->validated());
        }
    }
}
