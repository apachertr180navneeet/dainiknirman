<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\UploadImage;
use App\Http\Traits\UploadFile;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class SettingController extends Controller
{
    use UploadImage, UploadFile;

    protected $viewData;

    public function index()
    {
        $settingSiteTitle = Setting::select("value")->where("slug", "site-title")->first();
        $settingSiteLogo = Setting::select("value")->where("slug", "site-logo")->first();
        $settingLogoTitle = Setting::select("value")->where("slug", "logo-title")->first();
        $settingReservedRight = Setting::select("value")->where("slug", "reserved-right")->first();
        $settingItemPerPage = Setting::select("value")->where("slug", "show-items-per-page")->first();
        $settingAppVersion = Setting::select("value")->where("slug", "app-version")->first();
        $settingQrCodeImage = Setting::select("value")->where("slug", "qr-code-image")->first();
        $settingUpiId = Setting::select("value")->where("slug", "upi-id")->first();
        $settingRazorpayActiveMode = Setting::select("value")->where("slug", "razorpay-active-mode")->first();
        $settingRazorpayTestKey = Setting::select("value")->where("slug", "razorpay-test-key")->first();
        $settingRazorpayTestSecret = Setting::select("value")->where("slug", "razorpay-test-secret")->first();
        $settingRazorpayLiveKey = Setting::select("value")->where("slug", "razorpay-live-key")->first();
        $settingRazorpayLiveSecret = Setting::select("value")->where("slug", "razorpay-live-secret")->first();
        
        $data = [
            "site_title" => $settingSiteTitle->value,
            "site_logo" => $settingSiteLogo->value,
            "logo_title" => $settingLogoTitle->value,
            "reserved_right" => $settingReservedRight->value,
            "item_per_page" => $settingItemPerPage->value,
            "app_version" => $settingAppVersion->value,
            "qr_code_image" => $settingQrCodeImage->value,
            "upi_id" => $settingUpiId->value,
            "razorpay_active_mode" => $settingRazorpayActiveMode->value,
            "razorpay_test_key" => $settingRazorpayTestKey->value,
            "razorpay_test_secret" => $settingRazorpayTestSecret->value,
            "razorpay_live_key" => $settingRazorpayLiveKey->value,
            "razorpay_live_secret" => $settingRazorpayLiveSecret->value,
        ];

        $this->viewData["data"] = $data;

        return view("admin.settings.index")->with($this->viewData);
    }

    /**
     * Update Category.
     *
     * @return mixed
     *
     * @author Rajesh
     * @created 30 May 2022
     */
    public function update(Request $request)
    {
        // Get user
        $authUser = auth()->user();
        //----------

        $errorMessage = null;
        $imageCompressionPercent = 75;
        
        // Update Category
        DB::beginTransaction();

        try {
            // Update Settings
            Setting::where("slug", "site-title")->update(["value" => $request->site_title]);
            // Setting::where("slug", "site-logo")->update(["value" => $request->site_logo]);
            Setting::where("slug", "logo-title")->update(["value" => $request->logo_title]);
            Setting::where("slug", "reserved-right")->update(["value" => $request->reserved_right]);
            Setting::where("slug", "show-items-per-page")->update(["value" => $request->item_per_page]);
            Setting::where("slug", "upi-id")->update(["value" => $request->upi_id]);
            Setting::where("slug", "razorpay-active-mode")->update(["value" => $request->razorpay_active_mode]);

            if(isset($request->razorpay_test_key)){
                Setting::where("slug", "razorpay-test-key")->update(["value" => $request->razorpay_test_key]);
            }

            if(isset($request->razorpay_test_secret)){
                Setting::where("slug", "razorpay-test-secret")->update(["value" => $request->razorpay_test_secret]);
            }

            if(isset($request->razorpay_live_key)){
                Setting::where("slug", "razorpay-live-key")->update(["value" => $request->razorpay_live_key]);
            }

            if(isset($request->razorpay_live_secret)){
                Setting::where("slug", "razorpay-live-secret")->update(["value" => $request->razorpay_live_secret]);
            }

            // Upload QR code image
            if ($request->hasFile('qr_code_image'))
            {
                // Remove old image
                $settingQrCodeImage = Setting::select("value")->where("slug", "qr-code-image")->first();
                if(!empty($settingQrCodeImage->value)){
                    $deleted = Storage::disk("public")->delete("images/settings/".$settingQrCodeImage->value);
                }
                //-----------------

                $image = $this->uploadImage($request->file('qr_code_image'), "images/settings/", 70, null);
                if ($image['_status']) 
                {
                    $imageName = $image['_data'];
                    Setting::where("slug", "qr-code-image")->update(["value" => $imageName]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            $product = null;
            $errorMessage = $e->getMessage();
            DB::rollback();

            dd($e);
        }
        //------------

        // Set notification
        $notification = [
            '_status' => true,
            '_message' => __('messages.records_updated', ['record' => 'Setting']),
            '_type' => 'success',
        ];
        //-----------------

        return redirect()->route('admin.settings.index')->with(['notification' => $notification]);
    }
}