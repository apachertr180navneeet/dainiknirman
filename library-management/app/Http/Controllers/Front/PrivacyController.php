<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    public function index()
    {
        // Send view data
        $this->viewData['pageTitle'] = 'Privacy Policy';
        
        return view("front.privacy")->with($this->viewData);
    }
}