<?php

namespace App\Http\Controllers\Api\v1\frontEnd;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyCard;
use Illuminate\Http\Request;

use App\Traits\Helper;

class LoyaltyCardController extends Controller
{
    use Helper;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $loyaltyCard = LoyaltyCard::updateOrCreate(
            ['id' => isset($data['id'])?$data['id']:null], $data);
        $user = $this->getUpdatedUser($request->user()->id);
        $user->business_profile->update(['loyalty_card_status' => true]);
        return response()->json([
            'status' => true,
            'user' => $this->getUpdatedUser($request->user()->id),
            'loyaltyCard' => $loyaltyCard,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(LoyaltyCard $loyaltyCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoyaltyCard $loyaltyCard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoyaltyCard $loyaltyCard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoyaltyCard $loyaltyCard)
    {
        //
    }
}
