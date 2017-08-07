<?php

namespace App\Http\Controllers;

use App\Pustaha;
use App\Simsdm;
use App\User;
use App\UserAuth;
use App\Incentive;
use App\Approval;
use App\Http\Requests\StoreApprovalRequest;
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
            $login->payload->identity = env('LOGIN_USERNAME');
            $login->payload->user_id = env('LOGIN_ID');
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
            $user_auth = UserAuth::where('username',$user->username)->first();
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

        $approval = $pustaha->approval()->where('approval_status','UP')->orderBy('item', 'desc')->first();

        if(!empty($approval)){
            $var_item = $approval->item;
        }else{
            $var_item = 0;
        }

        $greater_item = $pustaha->approval()->where('item','>',$var_item)->where('approval_status', 'like', '%'.$type)->orderBy('item', 'desc')->first();

        $disabled_approv = null;
        if(!empty($greater_item)){
            $disabled_approv = 'disabled';
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
        return view('approval.approval-detail', compact(
            'upd_mode',
            'page_title',
            'action_url',
            'disabled',
            'pustaha',
            'pustaha_items',
            'incentive_ids',
            'type',
            'disabled_approv'
        ));
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

    public function store(StoreApprovalRequest $request)
    {
        $last_item = Approval::where('pustaha_id',$request->pustaha_id)->orderBy('id', 'desc')->first();
        $approval = new Approval();
        $approval->pustaha_id =  $request->pustaha_id;
        $approval->item = $last_item->item + 1;
        $approval->approval_status = $request->approve_status;
        $approval->approval_annotation = $request->annotation;
        $approval->incentive_id = $request->incentive_id;
        $approval->created_by = Auth::user()->user_id;

        DB::transaction(function () use ($approval, $request)
        {
            $pus_apprv = Pustaha::find($request->pustaha_id);

            if($request->type == "wr3"){
                $pus_apprv->approved_by_1 = Auth::user()->user_id;
            }elseif($request->type == "lp"){
                $pus_apprv->approved_by_2= Auth::user()->user_id;
            }
            $approval_pustaha = $pus_apprv->save();

            if($approval_pustaha){
                $approval->save();
            }
        });

        $request->session()->flash('alert-success', 'Pustaha berhasil di-approve');

        return redirect()->intended('/approvals');
    }

    public function getAjax()
    {
        $pustahas = Pustaha::all();

        $data = [];

        $i = 0;
        foreach ($pustahas as $pustaha)
        {
            if ($pustaha->pustaha_type == 'BUKU' ||
                $pustaha->pustaha_type == 'JURNAL-N' || $pustaha->pustaha_type == 'JURNAL-I' ||
                $pustaha->pustaha_type = -'PROSIDING'
            )
            {
                $pustaha_items = $pustaha->pustahaItem()->get();
                $co_authors = '';
                foreach ($pustaha_items as $pustaha_item)
                {
                    if (! empty($pustaha_item->username))
                    {
                        $full_name = $this->simsdm->getEmployee($pustaha_item->username)->full_name;
                        if (! empty($full_name))
                            $co_authors = $co_authors . $full_name . '; ';
                    } else
                    {
                        $co_authors = $co_authors . $pustaha_item->name . '; ';
                    }
                }
            }

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
            $data['data'][$i][6] = $pustaha->author;
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
