<?php

namespace App\Http\Controllers\Api\v1\frontEnd;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

use App\Traits\Helper;
use Validator;

class ItemController extends Controller
{
    use Helper;

    private $basePath = 'app/public/';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $this->getUpdatedUser($request->user()->id);
        $items = Item::whereNull('deleted_at')
            ->where('business_profiles_id', $user->business_profile->id)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json([
            'status' => true,
            'user' => $user,
            'items' => $items,
        ]);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'business_profiles_id' => 'required',
            'image' => 'required',
        ],[
            'name.required' => 'name_required',
            'price.required' => 'price_required',
            'description.required' => 'description_required',
            'business_profiles_id.required' => 'business_profiles_id_required',
            'image.required' => 'image_required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $data = $request->all();
        /////////////////////////////////////////////////////
        $directory = 'uploads/items';
        $file = $request->file("image");
        if (is_null($file)) {
            return response()->json([
                'status' => false,
                'user' => $this->getUpdatedUser($request->user()->id),
                'message' => 'no_file',
            ]);
        }
        $date = date('Y-m-d');
        $path = storage_path($this->basePath.$directory.'/'.$date);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        //$user->{$requestname} = $date.'/'.$name;
        /////////////////////////////////////////////
        $img = \Image::make($file->getRealPath());
        if ($img->width() > 700) {
            $img->orientate();
            $img->resize(500,null, function ($constraint) { $constraint->aspectRatio(); });
        }
        $filesizeInBytes = $img->filesize();
        $filePath = $date.'/'.$name;
        $data['images'][0] = $filePath;
        $img->save($path."/".$name,40);
        /////////////////////////////////////////////////////
        $item = Item::updateOrCreate(
            ['id' => isset($data['id'])?$data['id']:null], $data);
        $user = $this->getUpdatedUser($request->user()->id);
        $items = Item::where('business_profiles_id', $data['business_profiles_id'])
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();
        return response()->json([
            'status' => true,
            'user' => $user,
            'item' => $item,
            'items' => $items,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Item $item)
    {
        $item->delete();

        $user = $this->getUpdatedUser($request->user()->id);
        $items = Item::where('business_profiles_id', $user->business_profile->id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();
        return response()->json([
            'status' => true,
            'user' => $user,
            'item' => $item,
            'items' => $items,
        ]);
    }
}
