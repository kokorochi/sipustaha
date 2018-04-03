<?php

namespace App\Http\Controllers;

use App\Pustaha;
use App\Simsdm;
use App\Research;
use App\User;
use App\UserAuth;
use App\Incentive;
use App\Approval;
use App\Diseminasi;
use App\Http\Requests\StoreApprovalRequest;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use parinpan\fanjwt\libs\JWTAuth;
use View;

class ApprovalController extends MainController {
    public function __construct()
    {
        $this->middleware('is_auth');
        $this->middleware('is_operator');
        parent::__construct();

        $this->simsdm = new Simsdm();

        array_push($this->css['pages'], 'global/plugins/bower_components/fontawesome/css/font-awesome.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/animate.css/animate.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/jquery-ui/themes/base/jquery-ui.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/dataTables.bootstrap.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/datatables.responsive.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/select2/select2.min.css');

        array_push($this->js['plugins'], 'global/plugins/bower_components/datatables/jquery.dataTables.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/select2/select2.full.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/ui/minified/autocomplete.min.js');

        array_push($this->js['scripts'], 'global/plugins/bower_components/datatables/dataTables.bootstrap.min.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/datatables/datatables.responsive.js');

        array_push($this->js['scripts'], 'js/customize.js');

        View::share('css', $this->css);
        View::share('js', $this->js);
    }

    public function index()
    {
        if (env('APP_ENV') == 'local')
        {
            $login = new \stdClass();
            $login->logged_in = true;
            $login->payload = new \stdClass();
            // $login->payload->identity = env('LOGIN_USERNAME');
            // $login->payload->user_id = env('LOGIN_ID');
            $login->payload->identity = env('USERNAME_LOGIN');
            $login->payload->user_id = env('ID_LOGIN');
        } else
        {
            $login = JWTAuth::communicate('https://akun.usu.ac.id/auth/listen', @$_COOKIE['ssotok'], function ($credential)
            {
                $loggedIn = $credential->logged_in;
                if ($loggedIn)
                {
                    return $credential;
                } else
                {
                    setcookie('ssotok', null, -1, '/');

                    return false;
                }
            }
            );
        }
        if (! $login)
        {
            $login_link = JWTAuth::makeLink([
                'baseUrl'  => 'https://akun.usu.ac.id/auth/login',
                'callback' => url('/') . '/callback.php',
                'redir'    => url('/'),
            ]);

            return view('landing-page', compact('login_link'));
        } else
        {
            $user = new User();
            $user->username = $login->payload->identity;
            $user->user_id = $login->payload->user_id;
            Auth::login($user);

            $this->setUserInfo();

            $page_title = 'Daftar Pustaha';
            $user_auth = UserAuth::where('username',$user->user_id)->first();
            $auths = $user_auth->auth_type;

            return view('approval.approval-list', compact('page_title','auths'));
        }
    }

    public function showApproval()
    {
        $id = Input::get('id');
        $type = Input::get('type');
        $incentive_ids = Incentive::all();
        $pustaha = Pustaha::find($id);

        $disabled_approv = null;

        $approval = $pustaha->approval()->orderBy('item','desc')->first();

        if (strpos($approval->approval_status, strtoupper($type)) !== false) {
            $disabled_approv = 'disabled';
        }else{
            $approval = $pustaha->approval()->where('approval_status', 'like', 'AC:%'.$type)->first();
            if(isset($approval)){
                $disabled_approv = 'disabled';
            }
        }

        if (empty($pustaha) || $type!= "lp" && $type!= "wr3")
        {
            return abort('404');
        }

        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');
        array_push($this->css['pages'], 'kartik-v/bootstrap-fileinput/css/fileinput.min.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['scripts'], 'kartik-v/bootstrap-fileinput/js/fileinput.min.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery.inputmask/dist/jquery.inputmask.bundle.min.js');

        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/ui/minified/autocomplete.min.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'display';
        $action_url = 'approvals/create';
        $page_title = 'Approve Pustaha';
        $disabled = 'disabled';

        $pustaha_items = $pustaha->pustahaItem()->get();
        $simsdm = new Simsdm();
        foreach ($pustaha_items as $pustaha_item)
        {
            if (! empty($pustaha_item['item_username']))
            {
                $employee = $simsdm->getEmployee($pustaha_item['item_username']);
                $pustaha_item['item_external'] = null;
                $pustaha_item['item_username_display'] = 'NIP: ' . $pustaha_item['item_username'] . ', NIDN:' . $employee->nidn . ', Nama: ' . $employee->full_name;
            } else
            {
                $pustaha_item['item_external'] = 'X';
            }
        }

        $res = new Research();
        $research = $res->getResearchById($pustaha->research_id);
        
        $full_name = $this->simsdm->searchEmployee($research->author,1)->data[0]->full_name;
        $pustaha->research_full = 'Author: ' . $full_name . ', Judul Penelitian: ' . $research->title;
        $pustaha->author = $this->simsdm->searchEmployee($research->author,1)->data[0]->nidn;

        $dissemination = $pustaha->diseminasi()->first();

        $approval = $pustaha->approval()->where('approval_status', 'like', '%'.$type)->first();

        return view('approval.approval-detail', compact(
            'upd_mode',
            'page_title',
            'action_url',
            'disabled',
            'pustaha',
            'pustaha_items',
            'incentive_ids',
            'type',
            'dissemination',
            'disabled_approv',
            'approval'
        ));
    }

    public function store(StoreApprovalRequest $request)
    {
        $pustaha = Pustaha::find($request->pustaha_id);
        
        $last_item = $pustaha->approval()->orderBy('id', 'desc')->first();
        
        $pustaha->approval = new Approval();        
        $pustaha->approval->item = $last_item->item + 1;
        $pustaha->approval->approval_status = $request->approve_status;
        $pustaha->approval->approval_annotation = $request->annotation;

        if($request->type == "wr3"){
            $pustaha->approval->incentive_id = $request->incentive_id;    
        }
        
        $pustaha->approval->created_by = Auth::user()->user_id;

        DB::transaction(function () use ($pustaha, $request)
        {
            if($request->type == "wr3"){
                $pustaha->approved_by_1 = Auth::user()->user_id;
            }elseif($request->type == "lp"){
                $pustaha->approved_by_2= Auth::user()->user_id;
            }

            $pustaha->approval()->save($pustaha->approval);
        });

        $request->session()->flash('alert-success', 'Pustaha berhasil di-approve');

        return redirect()->intended('/approvals');
    }

    public function downloadDocument()
    {
        $input = Input::get();
        if (! isset($input['id']) || ! isset($input['type']))
        {
            return abort('404');
        }
        $pustaha = Pustaha::find($input['id']);

        if (! is_null($pustaha))
        {
            if ($pustaha->pustaha_type == 'BUKU')
                $path = 'book';
            elseif ($pustaha->pustaha_type == 'JURNAL-N' || $pustaha->pustaha_type == 'JURNAL-I')
                $path = 'journal';
            elseif ($pustaha->pustaha_type == 'PROSIDING')
                $path = 'proceeding';
            elseif ($pustaha->pustaha_type == 'HKI')
                $path = 'hki';
            elseif ($pustaha->pustaha_type == 'PATEN')
                $path = 'patent';
            $path = $path . '/' . $pustaha->id;
            $path = Storage::url('upload/' . Auth::user()->user_id . '/' . $path . '/');


            if ($input['type'] == '1')
            {
                $path = storage_path() . '/app' . $path . '/' . $pustaha->file_name;
                return response()->download($path, $pustaha->file_name_ori);
            } elseif ($input['type'] == '2')
            {
                $path = storage_path() . '/app' . $path . '/' . $pustaha->file_claim_request;
                return response()->download($path, $pustaha->file_claim_request_ori);
            } elseif ($input['type'] == '3')
            {
                $path = storage_path() . '/app' . $path . '/' . $pustaha->file_claim_accomodation;
                return response()->download($path, $pustaha->file_claim_accomodation_ori);
            } elseif ($input['type'] == '4')
            {
                $path = storage_path() . '/app' . $path . '/' . $pustaha->file_certification;
                return response()->download($path, $pustaha->file_certification_ori);
            }
        } else
        {
            return abort('404');
        }

    }

    public function getAjax()
    {
        $pustahas = new Collection();
        $auth = UserAuth::where('username',$this->user_info['user_id'])->first();
        if($auth->auth_type == 'OPEL'){
            $diseminasis = Diseminasi::all();
            foreach ($diseminasis as $diseminasi) {
                $pustaha = Pustaha::find($diseminasi->pustaha_id);
                
                $pustaha_item = new Pustaha();
                $pustaha_item->id = $pustaha->id;
                $pustaha_item->pustaha_type = $pustaha->pustaha_type;
                $pustaha_item->title = $pustaha->title;
                $pustaha_item->pustaha_date = $pustaha->pustaha_date;
                $pustaha_item->isbn_issn = $pustaha->isbn_issn;
                $pustaha_item->registration_no = $pustaha->registration_no;
                $pustaha_item->author = $pustaha->author;
                $pustaha_item->pustaha_date = $pustaha->pustaha_date;
                $pustaha_item->pustaha_date = $pustaha->pustaha_date;

                $pustahas->push($pustaha_item);
            }
        }elseif($auth->auth_type == 'OWR3'){

        }elseif($auth->auth_type == 'SU'){
            $pustahas = Pustaha::all();
        }else{
            $pustahas = new Pustaha();
        }

        $simsdm = new Simsdm();

        $data = [];

        $i = 0;

        foreach ($pustahas as $pustaha)
        {
            $approval = Approval::where('pustaha_id', $pustaha->id)->orderBy('id', 'desc')->first();
            $status = $approval->statusCode()->first();

            $data['data'][$i][0] = $pustaha->id;
            $data['data'][$i][1] = $i + 1;
            $data['data'][$i][2] = $pustaha->pustaha_type;
            $data['data'][$i][3] = $pustaha->title;
            $data['data'][$i][4] = $pustaha->pustaha_date;
            if (! empty($pustaha->isbn_issn))
                $data['data'][$i][5] = $pustaha->isbn_issn;
            else
                $data['data'][$i][5] = $pustaha->registration_no;
            $author = $simsdm->getEmployee($pustaha->author);
            $data['data'][$i][6] = $author->full_name;
            $data['data'][$i][7] = $status->code_description;

            $i++;
        }

        $count_data = count($data);
        if ($count_data == 0)
        {
            $data['data'] = [];
        } else
        {
            $count_data = count($data['data']);
        }
        $data['iTotalRecords'] = $data['iTotalDisplayRecords'] = $count_data;
        $data = json_encode($data, JSON_PRETTY_PRINT);

        return response($data, 200)->header('Content-Type', 'application/json');
    }
}
