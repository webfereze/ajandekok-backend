<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canvas;

class CanvasController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        foreach ($data as $item) {
            $canvas = new Canvas;
            $canvas->dimension = $item['dimension'];
            $canvas->price = $item['price'];
            $canvas->save();
        }

        return response()->json(['message' => 'Canvas records created successfully'], 201);
    }

    public function edit(Request $request, $id)
    {
        $canvas = Canvas::find($id);
        if (!$canvas) {
            return response()->json(['message' => 'Canvas not found'], 404);
        }

        $canvas->dimension = $request->input('dimension');
        $canvas->price = $request->input('price');
        $canvas->save();
        $canvas = Canvas::all();

        return response()->json($canvas);
    }

    public function delete($id)
    {
        $canvas = Canvas::find($id);
        if (!$canvas) {
            return response()->json(['message' => 'Canvas not found'], 404);
        }

        $canvas->active = 0;
        $canvas->save();
        $canvas = Canvas::where('active', 1)->get();

        return response()->json($canvas);
    }

    public function getData()
    {
        $canvas = Canvas::where('active', 1)->get();

        return response()->json($canvas);
    }

    public function updateStatus($id)
    {
        $canvas = Canvas::find($id);

        if (!$canvas) {
            return response()->json(['message' => 'Canvas not found'], 404);
        }

        $newStatus = request('status');

        $canvas->update(['status' => $newStatus]);

        return response()->json(['message' => 'Canvas status has been changed successfully']);
    }
}
