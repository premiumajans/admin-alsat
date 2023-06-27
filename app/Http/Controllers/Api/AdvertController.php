<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Vacancy;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdvertController extends Controller
{
    public function __construct()
    {
        $this->middleware('apiMid', ['except' => ['show', 'index']]);
    }

    public function index()
    {
        return response()->json(['adverts' => Advert::where('end_time', '>', Carbon::now())->with('description')->get()], 200);
    }

    public function show($id)
    {
        if (Advert::where('id', $id)->where('end_time', '>', Carbon::now()) and Advert::where('id', $id)->exists()) {
            $advert = Advert::find($id);
            $advert->increment('view_count');
            return response()->json([
                'advert' => $advert,
            ], 200);
        } else {
            return response()->json([
                'advert' => 'advert-not-found',
            ], 404);
        }
    }

    public function premium()
    {

    }

    public function vip()
    {

    }

    public function delete($id)
    {
        try {
            if (Advert::where('id', $id)->exists()) {
                Advert::find($id)->delete();
                return response()->json([
                    'message' => 'advert-successfully-deleted',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'advert-not-found',
                ], 404);
            }
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
