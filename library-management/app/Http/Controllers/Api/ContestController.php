<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\UploadImage;
use App\Http\Traits\UploadFile;
use App\Models\Contest;
use App\Models\ContestAuthor;
use PDF;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Spatie\Browsershot\Browsershot;

class ContestController extends Controller
{
    use UploadImage, UploadFile;

    /**
     * Get contest
     */
    public function getContest(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Contest not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            // Get contest detail
            $contest = Contest::whereDate("date", date("Y-m-d"))->orderBy("id", "desc")->first();

            if(!empty($contest)){
                // Contest author
                $isAttempt = ContestAuthor::where('created_by', $user->id)->where('contest_id', $contest->id)->count();

                $status = true;
                $message = "Contest get successfully.";
                $data = [
                    'is_attempt' => $isAttempt,
                    'contest' => $contest
                ];
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Save contest
     */
    public function saveContest(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Some error occurred, contest cannot be saved.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                    'contest_id' => 'required|exists:contests,id,deleted_at,NULL,date,'.date("Y-m-d"),
                    'title' => 'required|unique:contests,title,NULL,NULL,deleted_at,NULL',
                    'description' => 'required|max:5000',
                    'remark' => 'nullable|max:1000',
                    'is_accept_terms' => 'required|in:1',
                ],
                [
                    'contest_id.required' => 'Contest Id is required.',
                    'contest_id.exists' => 'Contest not found.',
                    'title.required' => 'Title is required field.',
                    'title.unique' => 'Title must be unique.',
                    'description.required' => 'Description is required field.',
                    'description.max' => 'Description cannot be more than 5000 characters.',
                    'remark.max' => 'Remark cannot be more than 1000 characters.',
                    'is_accept_terms.required' => 'Please accept the terms.',
                    'is_accept_terms.in' => 'Please accept the terms.'
                ]
            );

            if($validation->fails()){
                $message = $validation->errors()->first();
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }
            
            // Get contest detail
            $contest = Contest::find($request->contest_id);

            // Save contest submit by author
            $contestData = [
                'contest_id' => $contest->id,
                'contest_title' => $contest->title,
                'contest_date' => $contest->date,
                'contest_description' => $contest->description,
                'title' => $request->title,
                'description' => $request->description,
                'remark' => $request->remark ?? null,
                'is_accept_terms' => $request->is_accept_terms,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'created_by' => $user->id,
                'updated_by' => $user->id
            ];

            // Contest author Create
            $contestAuthor = ContestAuthor::create($contestData);
            
            if(!empty($contestAuthor)){
                $status = true;
                $message = "Contest saved successfully.";
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Get contest authors result
     */
    public function getContestResult(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Contest not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            // $validation = Validator::make($request->all(), [
            //     'contest_id' => 'required|exists:contests,id,deleted_at,NULL',
            // ],
            // [
            //     'contest_id.required' => 'Contest Id is required.',
            //     'contest_id.exists' => 'Contest not found.'
            // ]);

            // if($validation->fails()){
            //     $message = $validation->errors()->first();
            //     $response = [
            //         'status' => $status,
            //         'message' => $message,
            //         'data' => $data
            //     ];

            //     return response()->json($response, $statusCode);
            // }

            // Get contest detail
            $contest = ContestAuthor::select("id", "contest_id", "contest_title", "contest_date", "contest_description", "title", "description", "remark", "rank", "admin_remark", "status", "created_by")
            ->with([
                "author" => function($query) use($request){
                    $query->select("id", "name", "gender", "profile_photo");
                }
            ])
            // ->where("contest_id", $request->contest_id)
            ->where("contest_date", date("Y-m-d", strtotime("-1 days")))
            ->whereNotNull("rank")
            ->orderBy("rank", "asc")
            ->get();

            if(!empty($contest)){
                $status = true;
                $message = "Contest result get successfully.";
                $data['result'] = $contest;
            }

            $data['image_path'] = Storage::disk('local')->url('images/user/');

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /**
     * Get result certificate
     */
    public function _getContestCertificate(Request $request, $contestId, $authorId)
    {
        $user = Auth::user();
        $status = false;
        $message = "Contest not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            // $validation = Validator::make($request->all(), [
            //     'contest_id' => 'required|exists:contests,id,deleted_at,NULL',
            //     'author_id' => 'required|exists:users,id,deleted_at,NULL',
            // ],
            // [
            //     'contest_id.required' => 'Contest Id is required.',
            //     'contest_id.exists' => 'Contest not found.',
            //     'author_id.required' => 'Author Id is required.',
            //     'author_id.exists' => 'Author not found.'
            // ]);

            // if($validation->fails()){
            //     $message = $validation->errors()->first();
            //     $response = [
            //         'status' => $status,
            //         'message' => $message,
            //         'data' => $data
            //     ];

            //     return response()->json($response, $statusCode);
            // }

            // Get contest detail
            $contest = ContestAuthor::select("id", "contest_id", "contest_title", "contest_date", "contest_description", "title", "description", "remark", "rank", "admin_remark", "status", "created_by")
            ->with([
                "author" => function($query) use($request){
                    $query->select("id", "name", "gender", "profile_photo");
                }
            ])
            ->where("contest_id", $contestId)
            ->where("created_by", $authorId)
            ->whereNotNull("rank")
            ->orderBy("rank", "asc")
            ->first();

            if(!empty($contest)){
                // Get Certificate html
                $data['contest'] = $contest;
                $data['certificateBgImagePath'] = Storage::disk('local')->url('contest/contest_certificate.png');
                $data['logo'] = Storage::disk('local')->url('logo.png');

                $html = view("front.contests.certificate.certificate")->with($data)->render();

                $chromePath = 'C:\Program Files\Google\Chrome\Application\chrome.exe';
                $path = storage_path('app/public/example.pdf');
                $pdf = Browsershot::html($html)
                ->setChromePath($chromePath)
                ->setNodeBinary('C:\Program Files\nodejs\node.exe')
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
                // ->setOption('viewport', ['width' => 1280, 'height' => 1024])
                ->setOption('printBackground', true)
                ->setOption('preferCSSPageSize', true)
                // ->setDelay(1000)
                ->format('A4')
                ->landscape()
                ->showBackground()
                ->margins(10, 10, 10, 10)
                // ->savePdf($path);
                ->pdf();

                // // return response()->download($path);
                // return $pdf;

                // $contentForPdf = $this->generateImageFromHtml($html);
                // dd($contentForPdf);
                // // echo $html; exit;
                // // $pdf = Pdf::loadHtml($html);
                // $pdf = Pdf::loadView("front.contests.certificate.certificate", $data);
                // $pdf->set_option('isHtml5ParserEnabled', true);
                // $pdf->set_option('isPhpEnabled', true);
                // $pdf->set_option('default_font', "Hind");
                // // $pdf->output();
                // // $domPdf = $pdf->getDomPDF();
                // return $pdf->stream();

                // // return PDF::loadHtml($html)
                // // ->setPaper('a4', 'landscape')
                // // ->stream('certificate.pdf');

                // // To download the PDF
                // return $pdf->download();

                $data['pdf'] = $pdf;
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    /* public function getContestCertificate(Request $request, $contestId, $authorId)
    {
        $user = Auth::user();
        $status = false;
        $message = "Contest not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            // $validation = Validator::make($request->all(), [
            //     'contest_id' => 'required|exists:contests,id,deleted_at,NULL',
            //     'author_id' => 'required|exists:users,id,deleted_at,NULL',
            // ],
            // [
            //     'contest_id.required' => 'Contest Id is required.',
            //     'contest_id.exists' => 'Contest not found.',
            //     'author_id.required' => 'Author Id is required.',
            //     'author_id.exists' => 'Author not found.'
            // ]);

            // if($validation->fails()){
            //     $message = $validation->errors()->first();
            //     $response = [
            //         'status' => $status,
            //         'message' => $message,
            //         'data' => $data
            //     ];

            //     return response()->json($response, $statusCode);
            // }

            // Get contest detail
            $contest = ContestAuthor::select("id", "contest_id", "contest_title", "contest_date", "contest_description", "title", "description", "remark", "rank", "admin_remark", "status", "created_by")
            ->with([
                "author" => function($query) use($request){
                    $query->select("id", "name", "gender", "profile_photo");
                }
            ])
            ->where("contest_id", $contestId)
            ->where("created_by", $authorId)
            ->whereNotNull("rank")
            ->orderBy("rank", "asc")
            ->first();

            if(!empty($contest)){
                // Get Certificate html
                $data['contest'] = $contest;
                $data['certificateBgImagePath'] = Storage::disk('local')->url('contest/contest_certificate.png');
                $data['logo'] = Storage::disk('local')->url('logo.png');

                $html = view("front.contests.certificate.certificate")->with($data)->render();

                $chromePath = 'C:\Program Files\Google\Chrome\Application\chrome.exe';
                $certificateName = 'certificate_'.time().'.pdf';
                $path = storage_path('app/public/'.$certificateName);
                $pdf = Browsershot::html($html)
                ->setChromePath($chromePath)
                ->setNodeBinary('C:\Program Files\nodejs\node.exe')
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
                // ->setOption('viewport', ['width' => 1280, 'height' => 1024])
                ->setOption('printBackground', true)
                ->setOption('preferCSSPageSize', true)
                // ->setDelay(1000)
                ->format('A4')
                ->landscape()
                ->showBackground()
                ->margins(10, 10, 10, 10)
                ->savePdf($path);

                $data['pdf'] = Storage::disk('local')->url($certificateName);
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    } */

    public function __getContestCertificate(Request $request, $contestId, $authorId)
    {
        $user = Auth::user();
        $status = false;
        $message = "Contest not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            // $validation = Validator::make($request->all(), [
            //     'contest_id' => 'required|exists:contests,id,deleted_at,NULL',
            //     'author_id' => 'required|exists:users,id,deleted_at,NULL',
            // ],
            // [
            //     'contest_id.required' => 'Contest Id is required.',
            //     'contest_id.exists' => 'Contest not found.',
            //     'author_id.required' => 'Author Id is required.',
            //     'author_id.exists' => 'Author not found.'
            // ]);

            // if($validation->fails()){
            //     $message = $validation->errors()->first();
            //     $response = [
            //         'status' => $status,
            //         'message' => $message,
            //         'data' => $data
            //     ];

            //     return response()->json($response, $statusCode);
            // }

            // Get contest detail
            $contest = ContestAuthor::select("id", "contest_id", "contest_title", "contest_date", "contest_description", "title", "description", "remark", "rank", "admin_remark", "status", "created_by")
            ->with([
                "author" => function($query) use($request){
                    $query->select("id", "name", "gender", "profile_photo");
                }
            ])
            ->where("contest_id", $contestId)
            ->where("created_by", $authorId)
            ->whereNotNull("rank")
            ->orderBy("rank", "asc")
            ->first();

            if(!empty($contest)){
                // Get Certificate html
                $data['contest'] = $contest;
                $data['certificateBgImagePath'] = Storage::disk('local')->url('contest/contest_certificate.png');
                $data['logo'] = Storage::disk('local')->url('logo.png');

                $html = view("front.contests.certificate.certificate")->with($data)->render();
                $certificateName = 'certificate_'.time().'.pdf';
                $path = storage_path('app/public/'.$certificateName);
                // $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(true)->save($path);

                Pdf::setOption(['dpi' => 150, 'defaultFont' => 'mangal']);
                $pdf = Pdf::loadView("front.contests.certificate.certificate", $data);
                return $pdf->stream();
                exit;
                $data['pdf'] = Storage::disk('local')->url($certificateName);
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e);
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    public function getContestCertificate(Request $request, $contestId, $authorId)
    {
        $user = Auth::user();
        $status = false;
        $message = "Contest not found.";
        $data = null;
        $statusCode = 200;

        DB::beginTransaction();
        try {
            // $validation = Validator::make($request->all(), [
            //     'contest_id' => 'required|exists:contests,id,deleted_at,NULL',
            //     'author_id' => 'required|exists:users,id,deleted_at,NULL',
            // ],
            // [
            //     'contest_id.required' => 'Contest Id is required.',
            //     'contest_id.exists' => 'Contest not found.',
            //     'author_id.required' => 'Author Id is required.',
            //     'author_id.exists' => 'Author not found.'
            // ]);

            // if($validation->fails()){
            //     $message = $validation->errors()->first();
            //     $response = [
            //         'status' => $status,
            //         'message' => $message,
            //         'data' => $data
            //     ];

            //     return response()->json($response, $statusCode);
            // }

            // Get contest detail
            $contest = ContestAuthor::select("id", "contest_id", "contest_title", "contest_date", "contest_description", "title", "description", "remark", "rank", "admin_remark", "status", "created_by")
            ->with([
                "author" => function($query) use($request){
                    $query->select("id", "name", "gender", "profile_photo");
                }
            ])
            ->where("contest_id", $contestId)
            ->where("created_by", $authorId)
            ->whereNotNull("rank")
            ->orderBy("rank", "asc")
            ->first();

            if(!empty($contest)){
                // Get Certificate html
                $data['contest'] = $contest;
                $data['certificateBgImagePath'] = Storage::disk('local')->url('contest/contest_certificate.png');
                $data['logo'] = Storage::disk('local')->url('logo.png');

                $html = view("front.contests.certificate.certificate")->with($data)->render();
                $data['html'] = $html;

                $message = "Contest found successfully.";
                $status = true;
            }

            DB::commit();
        } 
        catch (\Exception $e) {
            DB::rollback();
            dd($e);
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }

    // In your controller or service
    public function generateImageFromHtml($htmlContent)
    {
        // Generate image from HTML string
        $imagePath = storage_path('app/public/generated_image.png');
        Browsershot::html($htmlContent)
            ->windowSize(800, 600) // Set desired image dimensions
            ->save($imagePath);

        // Or, take a screenshot of a URL
        // Browsershot::url('https://example.com')
        //     ->windowSize(800, 600)
        //     ->save($imagePath);

        return asset('storage/generated_image.png');

        // Return the image or a URL to it in your API response
        return response()->json(['image_url' => asset('storage/generated_image.png')]);
    }

    public function deleteContestCertificate(Request $request)
    {
        $user = Auth::user();
        $status = false;
        $message = "Certificate not found.";
        $data = null;
        $statusCode = 200;

        try {
            $validation = Validator::make($request->all(), [
                'certificate_name' => 'required',
            ],
            [
                'certificate_name.required' => 'Certificate name is required.'
            ]);

            if($validation->fails()){
                $message = $validation->errors()->first();
                $response = [
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ];

                return response()->json($response, $statusCode);
            }

            $isCertificateExists = Storage::disk('public')->exists($request->certificate_name);

            if($isCertificateExists){
                Storage::disk('public')->delete($request->certificate_name);
                $status = true;
                $message = "File deleted successfully.";
            }
        } 
        catch (\Exception $e) {
            dd($e->getMessage());
            
            $message = $e->getMessage();
            $data = null;
            $statusCode = 500;
        }

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $statusCode);
    }
}
