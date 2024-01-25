<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        foreach ($data as $item) {
            $coupon = new Coupon;
            $coupon->name = $item['name'];
            $coupon->value = $item['value'];
            $coupon->active = $item['active'];
            $coupon->qty = $item['qty'];
            $coupon->save();
        }

        return response()->json(['message' => 'Coupon created successfully'], 201);
    }

    public function edit(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        $coupon->name = $request->input('name');
        $coupon->value = $request->input('value');
        $coupon->active = $request->input('active');
        $coupon->qty = $request->input('qty');
        $coupon->save();
        $coupon = Coupon::all();

        return response()->json($coupon);
    }

    public function delete($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        $coupon->delete();
        $coupons = Coupon::get();

        return response()->json($coupons);
    }

    public function getData()
    {
        $coupons = Coupon::get();

        return response()->json($coupons);
    }

    public function checkCouponExists(Request $request)
    {
        $couponName = $request->input('name');
        $coupon = Coupon::where('name', $couponName)->where('active', 1)->first();

        if ($coupon) {
            return response()->json([
                'exists' => true,
                'details' => $coupon
            ]);
        } else {
            return response()->json([
                'exists' => false,
                'details' => null
            ]);
        }
    }
}
