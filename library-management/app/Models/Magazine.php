<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Orderable;
use App\Http\Traits\Statusable;
use App\Http\Traits\StatusToggleable;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\UploadImage;
use App\Http\Traits\UploadFile;

class Magazine extends Model
{
    use SoftDeletes, Statusable, StatusToggleable, UploadImage, UploadFile;

    protected $table = "magazines";

    protected $fillable = [
        'title',
        'type',
        'description',
        'cover_picture',
        'file_name',
        'date',
        'genre_name',
        'author_name',
        'mobile_number',
        'is_accept_terms',
        'status',
        'created_by',
        'updated_by',
        'is_selected'
    ];

    /**
     * Get magazines list
     */
    public function scopeGetMagazines($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = Magazine::select('magazines.id', 'magazines.title', 'magazines.date', 'magazines.type', 'magazines.cover_picture', 'magazines.status', 'magazines.created_at', 'magazines.is_selected')
        ->where(function($query){
            //
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(magazines.title) LIKE \'%'.$search.'%\' )');
            }

            // Filter
            if(isset($filter['filter_magazine_type']) && !empty($filter['filter_magazine_type']) && $filter['filter_magazine_type'] == 'u')
            {
                $filterMagazineType = strtolower($filter['filter_magazine_type']);
                $query->whereRaw('created_by != '.(config('constants.roles.ADMIN.value')));
            }
            else{
                $query->where('created_by', config('constants.roles.ADMIN.value'));
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "magazines.title",
                "magazines.type",
                // "magazines.date",
                "magazines.created_at",
                "magazines.status",
                ""
            );

            if(isset($filter['filter_magazine_type']) && !empty($filter['filter_magazine_type']) && $filter['filter_magazine_type'] == 'u'){
                $searchIndex = array_search('magazines.type', $arr_fields);

                if ($searchIndex !== false) {
                    array_splice($arr_fields, $searchIndex + 1, 0, "magazines.is_selected");
                }
            }
            else{
                array_splice($arr_fields, 3, 0, "magazines.date");
            }

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('magazines.id', 'desc');
        }

        // Set final limit and records
        if(!empty($limit))
        {
            $records = $records->skip($offset)->take($limit);
            return $records->get();
        }
        else
        {
            return $records->get()->count();
        }
    }

    /**
     * Save New Magazine
     */
    public function scopeSaveMagazine($model, $request)
    {
        // dd($request->all());
        // dd(storage_path('/'));
        // Get user
        $authUser = auth()->user();
        //----------

        $requestArray = $request->all();

        // Prepare data
        $date = null;

        if(!empty($requestArray['date']))
        {
            $date = date("Y-m-d", strtotime($requestArray['date']));
        }

        $data = [
            'title' => $requestArray['title'],
            'date' => $date,
            'type' => $requestArray['type'],
            'description' => $requestArray['description'] ?? null,
            'status' => $requestArray['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $authUser->id,
            'updated_by' => $authUser->id
        ];

        // Upload image and add to data array
        if ($request->hasFile('cover_picture'))
        {
            if(!file_exists(storage_path("app/public/magazine/cover/"))){
                mkdir(storage_path("app/public/magazine/cover/"), 0777, true);
            }
            else
            {
                chmod(storage_path("app/public/magazine/cover/"), 0777);
            }

            $image = $this->uploadImage($request->file('cover_picture'), "magazine/cover/", 70, null);

            if ($image['_status']) 
            {
                $imageName = $image['_data'];
                $data['cover_picture'] = $imageName;
            }
        }

        // Upload image and add to data array
        if ($request->hasFile('magazine_pdf'))
        {
            if(!file_exists(storage_path("app/public/magazine/pdf/"))){
                mkdir(storage_path("app/public/magazine/pdf/"), 0777, true);
            }
            else
            {
                chmod(storage_path("app/public/magazine/pdf/"), 0777);
            }

            $file = $this->uploadFile($request->file('magazine_pdf'), "magazine/pdf/", null);

            if ($file['_status']) 
            {
                $fileName = $file['_data'];
                $data['file_name'] = $fileName;
            }
        }

        $magazine = $this->create($data);

        return $magazine;
    }

    /**
     * Update magazine
     */
    public function scopeUpdateMagazine($model, $request)
    {
        $authUser = auth()->user();
        $magazine = null;
        $data = [];
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get Magazine
        $magazineData = Magazine::where('id', $requestArray['magazine_id'])->first();

        if(!empty($magazineData))
        {
            if(isset($requestArray['filter_magazine_type']) && $requestArray['filter_magazine_type'] == 'u'){
                $data = [
                    'is_selected' => $requestArray['is_selected'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $authUser->id
                ];
            }
            else{
                // Prepare data
                $date = $magazineData->date;

                if(!empty($requestArray['date']))
                {
                    $date = date("Y-m-d", strtotime($requestArray['date']));
                }

                $data = [
                    'title' => $requestArray['title'],
                    'date' => $date,
                    'type' => $requestArray['type'],
                    'description' => $requestArray['description'] ?? null,
                    'status' => $requestArray['status'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $authUser->id
                ];

                // Upload image and add to data array
                if ($request->hasFile('cover_picture'))
                {
                    // Remove old image
                    if(!empty($magazineData->cover_picture)){
                        $deleted = Storage::disk("public")->delete("magazine/cover/".$magazineData->cover_picture);
                    }
                    //-----------------

                    if(!file_exists(storage_path("app/public/magazine/cover/"))){
                        mkdir(storage_path("app/public/magazine/cover/"), 0777, true);
                    }
                    else
                    {
                        chmod(storage_path("app/public/magazine/cover/"), 0777);
                    }

                    $image = $this->uploadImage($request->file('cover_picture'), "magazine/cover/", 70, null);

                    if ($image['_status']) 
                    {
                        $imageName = $image['_data'];
                        $data['cover_picture'] = $imageName;
                    }
                }

                // Upload image and add to data array
                if ($request->hasFile('magazine_pdf'))
                {
                    // Remove old file
                    if(!empty($magazineData->file_name)){
                        $deleted = Storage::disk("public")->delete("magazine/pdf/".$magazineData->file_name);
                    }
                    //-----------------

                    if(!file_exists(storage_path("app/public/magazine/pdf/"))){
                        mkdir(storage_path("app/public/magazine/pdf/"), 0777, true);
                    }
                    else
                    {
                        chmod(storage_path("app/public/magazine/pdf/"), 0777);
                    }

                    $file = $this->uploadFile($request->file('magazine_pdf'), "magazine/pdf/", null, null);

                    if ($file['_status']) 
                    {
                        $fileName = $file['_data'];
                        $data['file_name'] = $fileName;
                    }
                }
            }

            if(!empty($data)){
                $magazine = $magazineData->update($data);
            }
        }

        return $magazine;
    }
}
