<?php

namespace App\Http\Controllers\Api\v1\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\LoyaltyCard;
use Illuminate\Http\Request;

use App\Traits\Helper;

class BusinessProfileController extends Controller
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
        $businessProfile = BusinessProfile::updateOrCreate(
            ['id' => isset($data['id'])?$data['id']:null], $data);
        $loyaltyCard = LoyaltyCard::where('business_profiles_id', $businessProfile->id)
            ->first();
        if (is_null($loyaltyCard)) {
            $businessProfile->update(['loyalty_card_status' => false]);
        }
        $user = $this->getUpdatedUser($request->user()->id);
        return response()->json([
            'status' => true,
            'user' => $user,
            'business_profile' => $businessProfile,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, BusinessProfile $businessProfile)
    {
        $businessProfile = BusinessProfile::where('id', $businessProfile->id)
            ->with('business_type')
            ->with('loyalty_card')
            ->with('items')->first();
        return response()->json([
            'status' => true,
            'user' => $this->getUpdatedUser($request->user()->id),
            'business_profile' => $businessProfile,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessProfile $businessProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessProfile $businessProfile)
    {
        
        return response()->json([
            'status' => true,
            'businessProfile' => $businessProfile,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessProfile $businessProfile)
    {
        //
    }
}
