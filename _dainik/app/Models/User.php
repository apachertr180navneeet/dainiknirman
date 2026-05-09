<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Orderable;
use App\Http\Traits\Statusable;
use App\Http\Traits\StatusToggleable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, Statusable, StatusToggleable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'mobile',
        'gender',
        'username',
        'password',
        'address',
        'profile_photo',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(){
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function account_detail(){
        return $this->hasOne(UserAccountDetail::class, 'user_id', 'id');
    }

    /**
     * Get users list
     */
    public function scopeGetUsers($model, $limit = null, $offset = null, $search = null, $filter = array(), $sort = array())
    {
        $records = User::select('users.id', 'users.role_id', 'users.name', 'users.email', 'users.mobile', 'users.gender', 'users.status', 'users.created_at')
        ->with([
            'role' => function($query){
                $query->select('id', 'name');
            }
        ])
        ->where(function($query){
            $query->where('users.role_id', '!=', 1);
            $query->orWhereNull('users.role_id');
        })
        ->where(function($query) use($search, $filter, $sort){
            // Search
            if(!(empty($search)))
            {
                $search = strtolower($search);
                $query->whereRaw('( lower(users.name) LIKE \'%'.$search.'%\' or lower(users.email) LIKE \'%'.$search.'%\' or lower(users.mobile) LIKE \'%'.$search.'%\' )');
            }
        });
        
        // Sort Columns Conditions
        if((!(empty($sort)) && $sort['column'] > 0) || !empty($search))
        {
            $arr_fields = array(
                "", 
                "users.name",
                "users.gender",
                "users.email",
                "users.mobile",
                "users.created_at",
                "users.status",
                ""
            );

            if($arr_fields[$sort['column']] != "")
            {
                $records->orderBy($arr_fields[$sort['column']], $sort['dir']);
            }
        }
        else
        {
            $records->orderBy('users.id', 'desc');
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

    public function scopeSaveUser($model, $request)
    {
        // dd($request->all());
        // Get user
        $authUser = auth()->user();
        //----------

        $requestArray = $request->all();

        // Prepare data
        $data = [
            'name' => $requestArray['name'],
            'mobile' => $requestArray['mobile'],
            'email' => $requestArray['email'],
            'address' => $requestArray['address'] ?? null,
            'gender' => $requestArray['gender'] ?? null,
            'status' => $requestArray['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $authUser->id,
            'updated_by' => $authUser->id
        ];
        $user = $this->create($data);

        // Add bank details
        if(!empty($requestArray['account_holder_name']))
        {
            $dataBank = [
                'user_id' => $user->id,
                'account_holder_name' => $requestArray['account_holder_name'],
                'branch_name' => $requestArray['branch_name'],
                'city_name' => $requestArray['city_name'],
                'ifsc_code' => $requestArray['ifsc_code'] ?? null,
                'account_number' => $request['bank_account_number'] ?? null
            ];
            UserAccountDetail::create($dataBank);
        }

        return $user;
    }

    /**
     * Update user
     */
    public function scopeUpdateUser($model, $request)
    {
        $authUser = auth()->user();
        $user = null;
        
        $requestArray = $request->all();
        // dd($requestArray);

        // Get User
        $userData = User::where('id', $requestArray['user_id'])->first();

        if(!empty($userData))
        {
            // Prepare data
            $data = [
                'name' => $requestArray['name'],
                'mobile' => $requestArray['mobile'],
                'email' => $requestArray['email'],
                'address' => $requestArray['address'] ?? null,
                'gender' => $requestArray['gender'] ?? null,
                'status' => $requestArray['status'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $authUser->id
            ];
            $user = $userData->update($data);

            // Add bank details
            if(!empty($requestArray['account_holder_name']))
            {
                $userBankDetail = UserAccountDetail::where('user_id', $userData->id)->first();

                $dataBank = [
                    'user_id' => $userData->id,
                    'account_holder_name' => $requestArray['account_holder_name'],
                    'branch_name' => $requestArray['branch_name'],
                    'city_name' => $requestArray['city_name'],
                    'ifsc_code' => $requestArray['ifsc_code'] ?? null,
                    'account_number' => $request['bank_account_number'] ?? null
                ];

                if(!empty($userBankDetail))
                {
                    // Update
                    $userBankDetail->update($dataBank);
                }
                else
                {
                    // Create
                    UserAccountDetail::create($dataBank);
                }
            }
        }

        return $user;
    }
}
