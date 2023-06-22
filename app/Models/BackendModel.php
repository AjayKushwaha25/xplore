<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\{Outlet, TeamLead, DistributorSale, OrderItem, Brand, Campaign, Ref, SchemeOffer, SchemeLoginHistory, SchemeVisitOnLogin};

class BackendModel extends Model
{
    public static function getAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current',$tables);
        return $tables;
    }
    public static function getBrandsList(){
        $result = DB::table('brands')->where(['status'=>1])->get();
        return $result;
    }
    public static function getContestsList(){
        $result = Contest::get();
        return $result;
    }

    public static function saveOrderItems($data){
        $result = DB::table('order_items')->insertGetId($data);
        return $result;
    }

    public static function getOrderItemsforCurrentOutlet($orderID,$dsID){
        $result = DB::table('order_items')
                    ->where(['id'=>$orderID,'ds_id'=>$dsID])
                    ->get();
        return $result;
    }

    public static function getOutletForScannedQRCode($dsID,$uuid){
        $result = Outlet::where(['distributor_sale_id'=>$dsID,'status'=>1,'uuid'=>$uuid])
                    ->get();
        return $result;
    }

    public static function getOutletListsWithOutScannedQRCode($dsID,$uuid){
        $result = Outlet::where(['distributor_sale_id'=>$dsID,'status'=>1])
                    ->whereNotIn('uuid',[$uuid])
                    ->get();
        return $result;
    }

    public static function getDSDetailsByUUID($dsID){
        $result = DistributorSale::where(['id'=>$dsID,'status'=>1])
                    ->get();
        return $result;
    }

    public static function getOrderHistories($dsID,$outletUUID){
        $outletUUID = BackendModel::getOutletIDByUUID($outletUUID);
        $result = OrderItem::where(['ds_id'=>$dsID,'outlet_id'=>$outletUUID])
                    ->leftjoin('outlets','outlets.id','=','order_items.outlet_id')
                    ->select('outlets.outlet_name','order_items.id as oid','order_items.created_at', 'outlets.mobile_number')
                    ->orderBy('order_items.id','DESC')
                    ->get();
        return $result;
    }

    public static function getOutletDetailsForCurrentDS($dsID, $uuid){
        $result = Outlet::where(['distributor_sale_id'=>$dsID,'uuid'=>$uuid,'status'=>1])
                    // ->with('distributorSales')
                    ->first();
        return $result;
    }

    public static function getOrderItemsData($dsID, $outlet_id, $start_time, $end_time){
        $result = OrderItem::where(['ds_id'=>$dsID,'outlet_id'=>$outlet_id])
                    ->whereBetween('created_at',[$start_time, $end_time])
                    ->whereTime('created_at','>','09:00:00')
                    ->whereTime('created_at','<','19:00:00')
                    // ->select('order_details')
                    ->pluck('order_details');
                    // dd(count($result));
        return $result;
    }

    public static function getThreeDayDataForMonthlyCalc($dsID, $outlet_id, $start_time, $end_time){
        $result = [];
        $count =  OrderItem::where(['ds_id'=>$dsID,'outlet_id'=>$outlet_id])
                            ->whereBetween('created_at',[$start_time, $end_time])
                            ->count();
                            // dd($count);
        if($count >= 3){
            $result = OrderItem::where(['ds_id'=>$dsID,'outlet_id'=>$outlet_id])
                        ->whereBetween('created_at',[$start_time, $end_time])
                        ->take(3)
                        ->pluck('order_details');
        }
        return $result;
    }

    public static function todaysCampaignProduct($currentDate){
        $result = Campaign::where(['date'=>$currentDate])
                    ->value('brand_id');
        return $result;
    }

    public static function getDailyTargetQuantity($currentDate){
        $result = Campaign::where(['date'=>$currentDate])
                    ->value('slab');
        return $result;
    }

    public static function getBrandsIDAndName(){
        $result = Brand::where(['status'=>1])
                    ->select('id','name')
                    ->get();
        return $result;
    }

    public static function getBrandName($id){
        $result = Brand::where(['id'=>$id])
                    ->value('name');
        return $result;
    }

    public static function checkIfRefIDExists($refID){
        $result = Ref::where(['uuid'=>$refID])
                    ->count();
        return $result;
    }

    public static function checkIfOutletIDExists($outletUUID){
        $result = Outlet::where(['uuid'=>$outletUUID])
                    ->count();
        return $result;
    }

    public static function getUUIDFromDhanushID($dhanushID){
        $result = Outlet::where(['dhanush_id'=>$dhanushID])
                    ->value('uuid');
        return $result;
    }


    /* Counts */
    public static function getTotalCampaignsCount(){
        $result = Campaign::count();
        return $result;
    }
    public static function getOutletCountForDS($dsID){
        $result = Outlet::where(['distributor_sale_id'=>$dsID,'status'=>1])
                    ->count();
        return $result;
    }



    /* Common */
    public static function getRetailerIDBySession(){
        return $_SESSION['retailer']['retailer_id'] ?? '';
    }
    public static function getDSIDBySession(){
        return $_SESSION['ds']['ds_id'] ?? '';
    }
    public static function getTLIDBySession(){
        return $_SESSION['tl']['tl_id'] ?? '';
    }
    public static function getOutletIDByUUID($uuid){
        $result = Outlet::where(['uuid'=>$uuid,'status'=>1])
                    ->value('id');
        return $result;
    }
    public static function getDSIDByUUID($uuid){
        $result = DistributorSale::where(['uuid'=>$uuid])
                    ->value('id');
        return $result;
    }
    public static function getTLIDByUUID($uuid){
        $result = TeamLead::where(['uuid'=>$uuid])
                    ->value('id');
        return $result;
    }

    public static function getUserIDByUUID($uuid){
        $result = User::where(['uuid'=>$uuid,'status'=>1])
                    ->value('id');
        return $result;
    }




    /* TL */

    public static function getTLDetailsByID($tlID){
        $result = TeamLead::where(['id'=>$tlID,'status'=>1])
                    ->first();
        return $result;
    }
    public static function singleOutletDetail($tlID,$outletUUID){
        $result = Outlet::where(['team_lead_id'=>$tlID,'uuid'=>$outletUUID])
                    ->first();
        return $result;
    }



    /* Scheme Communication User */

    public static function getRetailerDetailsByUUID($tlID){
        $result = TeamLead::where(['id'=>$tlID,'status'=>1])
                    ->first();
        return $result;
    }

    public static function getOffersList(){
        $data = SchemeOffer::where('status',1)
                            ->get();
        return $data;
    }

    public static function getBrandListsForOffers(){
        $data = SchemeOffer::where('status',1)
                            ->select('id', 'uuid', 'brand')
                            ->where('status',1)
                            ->get();
        return $data;
    }

    public static function getComplaintRemarkList(){
        $data = SchemeComplaintRemark::where('status', 1)
                                    ->get();
        return $data;
    }

    public static function getRetailerDetails($retailerID){
        $data = Outlet::where(['status'=>1,'id'=>$retailerID])
                        ->select('id','uuid','owner_name','outlet_name','mobile_number','location')
                        ->first();
        return $data;
    }


    /* Scheme Communication Dashboard */

    public static function getLoginHistory(){
        $data = SchemeLoginHistory::get();
        return $data;
    }
    public static function getVisitOnLogin(){
        $data = SchemeVisitOnLogin::get();
        return $data;
    }
    public static function getOfferDetails(){
        $data=SchemeOffer::get();
        return $data;
    }
    public static function getComplaintRemarksDetails(){
        $data = SchemeComplaintRemark::get();
        return $data;
    }
    public static function getComplaintDetails(){
        $data = SchemeComplaint::with(['outlet:id,uuid,outlet_name,owner_name,mobile_number,location','schemeComplaintRemark:id,remark,uuid'])
                ->select('id','outlet_id','scheme_complaint_remark_id','uuid','salesmanname','complaint','resolve_status','action_remark','created_at')
                ->get();
        return $data;
    }
    public static function checkIfBrandNameExists($brand){
        $result = SchemeOffer::where('brand',$brand)
                            ->value('id');
        return $result;
    }

    public static function getOfferDetailsByID($uuid){
        $data=SchemeOffer::where('uuid',$uuid)
                        ->get();
        return $data;
    }
}
