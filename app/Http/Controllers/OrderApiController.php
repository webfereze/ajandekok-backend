<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderPhoto;
use App\Models\Canvas;
use App\Models\Coupon;
use Storage;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NewOrderNotification;
use App\Mail\NewOrderMail;

class OrderApiController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    public function index()
    {
        $orders = Order::with('orderPhotos.canvas')->get();
        $orders->each(function ($order) {
            $order->orderPhotos->each(function ($photo) use ($order) {
                $photo->url = asset('storage/orders/' . $order->id . '/' . $photo->image);
                $photo->size = Canvas::where('id', $photo->size)->value('dimension');
            });
        });
        return response()->json(['orders' => $orders], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'shipping' => 'string',
        ]);

        $totalPrice = 0;

        if($request->images){
            foreach ($request->images as $imageData) {
                $quantity = $imageData['quantity'];
                $sizeId = $imageData['dimensions'];

                $canvasPrice = Canvas::find($sizeId)->price;

                $imagePrice = $canvasPrice * $quantity;
                $totalPrice += $imagePrice;
            }
        }

        if($request->coupon_id){
            $coupon = Coupon::find($request->coupon_id);
            if($coupon->active == 0 || $coupon->qty <= 0){
                return response()->json(['message' => 'Coupon not found or inactive'], 404);
            }

            $totalPrice = $totalPrice - ($totalPrice * $coupon->value / 100);
        }

        if($request->shipping == 'curier'){
            $totalPrice += 20;
        }

        $order = new Order([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'address' => $request->address,
            'number' => $request->number,
            'country' => $request->country,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'phone' => $request->phone,
            'total_price' => $totalPrice,
            'details' => $request->details ?? "",
            'shipping' => $request->shipping,
            'coupon' => $request->coupon_id ? $coupon->name : null,
        ]);

        $order->save();

        if($request->coupon_id && $coupon->active == 1 && $coupon->qty > 0){
            if($coupon->qty == 0 || $coupon->qty-1 == 0){
                $coupon->active = 0;
            }
            $coupon->qty = $coupon->qty - 1;
            $coupon->save();
        }

        $orderPhotos = [];
        if($request->images){
            foreach ($request->images as $imageData) {
                $imageFile = $imageData['file'];
                $quantity = $imageData['quantity'];
                $sizeId = $imageData['dimensions'];
                $paperType = "";

                $orderFolder = 'orders/' . $order->id;
                if (!Storage::disk('public')->exists($orderFolder)) {
                    Storage::disk('public')->makeDirectory($orderFolder);
                }

                $fileName = uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imagePath = $imageFile->storeAs($orderFolder, $fileName, 'public');

                $orderPhoto = new OrderPhoto([
                    'order_id' => $order->id,
                    'image' => $fileName,
                    'size' => $sizeId,
                    'quantity' => $quantity,
                    'paper_type' => $paperType,
                ]);

                $orderPhoto->save();

                $orderPhotos[] = $orderPhoto;
            }
        }

        // return response()->json([
        //     'order' => $order,
        //     'order_photos' => $orderPhotos,
        // ], 201); // Răspuns "Created" (HTTP 201)

//         Mail::to('ferencziemil@gmail.com')->send(new NewOrderMail($order));

        return response()->json(['message' => 'The order success created'], 201);
    }

    public function updateStatus(Request $request, $order_id)
    {
        // Validare request
        $request->validate([
            'status' => 'required|in:1,2,3,4',
        ]);

        $order = Order::find($order_id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status was updated']);
    }

    public function deleteOrder($order_id)
    {
        $order = Order::find($order_id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orderPhotos = OrderPhoto::where('order_id', $order_id)->get();

        foreach ($orderPhotos as $photo) {
            $photoPath = 'public/orders/' . $order_id . '/' . $photo->image;

            if (Storage::exists($photoPath)) {

                Storage::delete($photoPath);
            }

            $photo->delete();
        }

        $order->delete();

        $folderPath = 'public/orders/' . $order_id;
        if (Storage::allFiles($folderPath) === []) {
            Storage::deleteDirectory($folderPath);
        }

        return response()->json(['message' => 'Order and images have been successfully deleted']);
    }
}
