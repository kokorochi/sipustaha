<?php

namespace App\Http\Controllers;

use View;
use App\Approval;
use App\Pustaha;
use App\PustahaItem;
use App\Research;
use App\Simsdm;
use App\User;
use App\UserAuth;
use App\Diseminasi;
use parinpan\fanjwt\libs\JWTAuth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePustahaRequest;
use App\Http\Requests\StoreDisseminationRequest;

class PustahaController extends MainController {
    protected $simsdm;

    public function __construct()
    {
        $this->middleware('is_auth')->except('index');
        parent::__construct();

        $this->simsdm = new Simsdm();

        array_push($this->css['pages'], 'global/plugins/bower_components/fontawesome/css/font-awesome.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/animate.css/animate.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/jquery-ui/themes/base/jquery-ui.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/dataTables.bootstrap.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/datatables.responsive.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/select2/select2.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css');

        array_push($this->js['plugins'], 'global/plugins/bower_components/datatables/jquery.dataTables.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/select2/select2.full.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/ui/minified/autocomplete.min.js');

        array_push($this->js['scripts'], 'global/plugins/bower_components/datatables/dataTables.bootstrap.min.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/datatables/datatables.responsive.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js');

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
//            $login->payload->identity = env('LOGIN_USERNAME');
//            $login->payload->user_id = env('LOGIN_ID');
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

            return view('pustaha.pustaha-list', compact('page_title'));
        }
    }

    public function create()
    {
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

        $upd_mode = 'create';
        $action_url = 'pustahas/create';
        $page_title = 'Tambah Pustaha';
        $disabled = '';

        $pustaha = new Pustaha();
        $simsdm = new Simsdm();
        $simpel = new Research();
        $pustaha->author = $simsdm->getEmployee($this->user_info['user_id'])->nidn;
        $pustaha->research_full = null;

        $pustaha_items = new Collection();
        $pustaha_item = new PustahaItem();
        $pustaha_item->item_external = null;
        $pustaha_items->push($pustaha_item);
        $researches = $simpel->getResearches($pustaha->author);


        foreach ($researches as $key => $research) {
            $check = Pustaha::where('research_id', $research->id)->count();
            if($check > 0){
                unset($researches[$key]);
            }
        }

        $auth = UserAuth::where('username',$this->user_info['user_id'])->first();

        if(empty($auth)) $auth = "";
        else $auth = $auth->auth_type;

        return view('pustaha.pustaha-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'pustaha',
            'pustaha_items',
            'researches',
            'auth'
        ));
    }

    public function store(StorePustahaRequest $request)
    {
        if ($request->pustaha_type == 'BUKU')
        {
            $path = 'book';
            $var_pustaha = $this->assignBook($request);
        } elseif ($request->pustaha_type == 'JURNAL-N' || $request->pustaha_type == 'JURNAL-I')
        {
            $path = 'journal';
            $var_pustaha = $this->assignJournal($request);
        } elseif ($request->pustaha_type == 'PROSIDING-N' || $request->pustaha_type == 'PROSIDING-I')
        {
            $path = 'proceeding';
            $var_pustaha = $this->assignProceeding($request);
        } elseif ($request->pustaha_type == 'HKI')
        {
            $path = 'hki';
            $var_pustaha = $this->assignHki($request);
        } elseif ($request->pustaha_type == 'PATEN')
        {
            $path = 'patent';
            $var_pustaha = $this->assignPatent($request);
        }

        if(!empty($request->is_simpel)){
            $var_pustaha->pustaha->is_simpel = 1;
            $var_pustaha->pustaha->research_id = $request->research_id;
        }else{
            $var_pustaha->pustaha->is_simpel = 0;
            $var_pustaha->pustaha->research_id = 0;
        }

        $var_pustaha->approval = new Approval();
        $var_pustaha->approval->item = 1;
        $var_pustaha->approval->approval_status = 'SB';
        $var_pustaha->approval->approval_annotation = 'Submitted';
        $var_pustaha->approval->created_by = Auth::user()->user_id;

        DB::transaction(function () use ($var_pustaha, $path, $request)
        {
            $saved_pustaha = $var_pustaha->pustaha->save();
            if ($saved_pustaha)
            {
                $path = $path . '/' . $var_pustaha->pustaha->id;
                if (isset($var_pustaha->pustaha_items))
                {
                    $var_pustaha->pustaha->pustahaItem()->saveMany($var_pustaha->pustaha_items);
                }
                $var_pustaha->pustaha->approval()->save($var_pustaha->approval);

                $path = Storage::url('upload/' . Auth::user()->user_id . '/' . $path . '/');
                $request->file('file_name_ori')->storeAs($path, $var_pustaha->pustaha->file_name);
                // $request->file('file_claim_request_ori')->storeAs($path, $var_pustaha->pustaha->file_claim_request);
                // $request->file('file_claim_accomodation_ori')->storeAs($path, $var_pustaha->pustaha->file_claim_accomodation);
                // $request->file('file_certification_ori')->storeAs($path, $var_pustaha->pustaha->file_certification);
            }
        });

        $request->session()->flash('alert-success', 'Pustaha berhasil dibuat');

        return redirect()->intended('/');
    }

    public function display()
    {
        $id = Input::get('id');
        $pustaha = Pustaha::find($id);
        if (empty($pustaha))
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
        $action_url = 'pustahas/edit';
        $page_title = 'Detail Pustaha';
        $disabled = 'disabled';

        $pustaha_items = $pustaha->pustahaItem()->get();
        $simsdm = new Simsdm();
        $simpel = new Research();

        foreach ($pustaha_items as $pustaha_item)
        {
            if (! empty($pustaha_item['item_username']))
            {
                $employee = $simsdm->getEmployee($pustaha_item['item_username']);
                $pustaha_item['item_external'] = null;
                $pustaha_item['item_username_display'] = 'NIP: ' . $employee->nip . ', NIDN:' . $employee->nidn . ', Nama: ' . $employee->full_name;
            } else
            {
                $pustaha_item['item_external'] = 'X';
            }
        }
        
        // $approval_rj = $pustaha->approval()->orderBy('id', 'DESC')->take(2)->get();
        // if($approval_rj[0]->approval_status=='RJ:WR3' || $approval_rj[1]->approval_status=='RJ:LP'){
        //     $edit = true;
        // }elseif($approval_rj[0]->approval_status=='AC:WR3' && $approval_rj[1]->approval_status=='RJ:LP'){
        //     $edit = true;
        // }elseif($approval_rj[0]->approval_status=='AC:LP' && $approval_rj[1]->approval_status=='RJ:WR3'){
        //     $edit = true;
        // }
        $edit = true;

        $diseminasi = Diseminasi::where('pustaha_id', $pustaha->id)->count();
        if($diseminasi > 0 ) $edit = false;
         
        $approval1 = $pustaha->approval()->get();
        $approvales = new Collection();
        foreach ($approval1 as $approv) {
            $app = new Collection();
            $status = $approv->statusCode()->first();
            $app->code = $approv->approval_status;
            $app->approval_annotation = $approv->approval_annotation;
            $app->approval_status = $status->code_description;
            $app->created_at = $approv->created_at;
            $approvales->push($app);
        }
        

        $res = new Research();
        if($pustaha->research_id != 0){
            $research = $res->getResearchById($pustaha->research_id);
            $full_name = $simsdm->searchEmployee($research->author,1)->data[0]->full_name;
            $pustaha->research_full = 'Author: ' . $full_name . ', Judul Penelitian: ' . $research->title;
            $pustaha->author = $this->simsdm->searchEmployee($research->author,1)->data[0]->nidn;
            $researches = $simpel->getResearches($pustaha->author);
        }else{
            $research = new Collection();
            $researches = new Collection();
        }

        $auth = UserAuth::where('username',$this->user_info['user_id'])->first();

        if(empty($auth)) $auth = "";
        else $auth = $auth->auth_type;

        $pustaha->pustaha_date = date('d-m-Y', strtotime($pustaha->pustaha_date));

        return view('pustaha.pustaha-detail', compact(
            'pustaha',
            'pustaha_items',
            'approvales',
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'research',
            'edit',
            'researches',
            'auth'
        ));
    }

    public function edit()
    {
        $id = Input::get('id');
        $pustaha = Pustaha::find($id);
        if (empty($pustaha))
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

        $upd_mode = 'edit';
        $action_url = 'pustahas/edit';
        $page_title = 'Update Pustaha';
        $disabled = '';

        $pustaha_items = $pustaha->pustahaItem()->get();
        $simsdm = new Simsdm();
        $simpel = new Research();
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
        $approvales = $pustaha->approval()->get();
        
        $res = new Research();
        $research = $res->getResearchById($pustaha->research_id);
        
        $full_name = $this->simsdm->searchEmployee($research->author,1)->data[0]->full_name;
        $pustaha->research_full = 'Author: ' . $full_name . ', Judul Penelitian: ' . $research->title;
        $pustaha->author = $this->simsdm->searchEmployee($research->author,1)->data[0]->nidn;
        $researches = $simpel->getResearches($pustaha->author);

        $auth = UserAuth::where('username',$this->user_info['user_id'])->first();

        if(empty($auth)) $auth = "";
        else $auth = $auth->auth_type;
        $pustaha->pustaha_date = date('d-m-Y', strtotime($pustaha->pustaha_date));

        return view('pustaha.pustaha-detail', compact(
            'pustaha',
            'pustaha_items',
            'approvales',
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'researches',
            'auth'
        ));
    }

    public function update(StorePustahaRequest $request)
    {
        if ($request->pustaha_type == 'BUKU')
        {
            $path = 'book';
            $var_pustaha = $this->assignBook($request);

        } elseif ($request->pustaha_type == 'JURNAL-N' || $request->pustaha_type == 'JURNAL-I')
        {
            $path = 'journal';
            $var_pustaha = $this->assignJournal($request);
        } elseif ($request->pustaha_type == 'PROSIDING-N' || $request->pustaha_type == 'PROSIDING-I')
        {
            $path = 'proceeding';
            $var_pustaha = $this->assignProceeding($request);
        } elseif ($request->pustaha_type == 'HKI')
        {
            $path = 'hki';
            $var_pustaha = $this->assignHki($request);
        } elseif ($request->pustaha_type == 'PATEN')
        {
            $path = 'patent';
            $var_pustaha = $this->assignPatent($request);
        }

        $last_item = Approval::where('pustaha_id',$request->id)->orderBy('id', 'desc')->first();

        $var_pustaha->approval = new Approval();
        $var_pustaha->approval->item = $last_item->item + 1;
        $var_pustaha->approval->approval_status = 'UP';
        $var_pustaha->approval->approval_annotation = 'Updated';
        $var_pustaha->approval->created_by = Auth::user()->user_id;

        DB::transaction(function () use ($var_pustaha, $path, $request)
        {
            PustahaItem::where('pustaha_id', $request->id)->delete(); 
         
            $saved_pustaha = $var_pustaha->pustaha->save();
         
            if ($saved_pustaha)
            {
                $path = $path . '/' . $var_pustaha->pustaha->id;
                if (isset($var_pustaha->pustaha_items))
                {
                    $var_pustaha->pustaha->pustahaItem()->saveMany($var_pustaha->pustaha_items);
                }
                $var_pustaha->pustaha->approval()->save($var_pustaha->approval);

                $path = Storage::url('upload/' . Auth::user()->user_id . '/' . $path . '/');
                if(!empty($request->file('file_name_ori')))
                {
                    $request->file('file_name_ori')->storeAs($path, $var_pustaha->pustaha->file_name);
                }
                // elseif (!empty($request->file('file_claim_request_ori'))){
                //     $request->file('file_claim_request_ori')->storeAs($path, $var_pustaha->pustaha->file_claim_request);
                // }elseif (!empty($request->file('file_claim_accomodation_ori'))){
                //     $request->file('file_claim_accomodation_ori')->storeAs($path, $var_pustaha->pustaha->file_claim_accomodation);
                // }elseif (!empty($request->file('file_certification_ori'))){
                //     $request->file('file_certification_ori')->storeAs($path, $var_pustaha->pustaha->file_certification);
                // }
            }
        });

        $request->session()->flash('alert-success', 'Pustaha berhasil di-update');

        return redirect()->intended('/');
    }

    public function diseminasi()
    {
        $id = Input::get('id');
        $pustaha = Pustaha::find($id);
        
        if (!empty($pustaha))
        {
            if($pustaha->pustaha_type != 'PROSIDING-N' && $pustaha->pustaha_type != 'PROSIDING-I')
                return abort('404');
        }else{
            return abort('404');
        }

        array_push($this->css['pages'], 'kartik-v/bootstrap-fileinput/css/fileinput.min.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['scripts'], 'kartik-v/bootstrap-fileinput/js/fileinput.min.js');

        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'diseminasi';
        $action_url = 'pustahas/diseminasi';
        $page_title = 'Permohonan Diseminasi';
        $disabled = '';

        $simsdm = new Simsdm();
        $simpel = new Research();
        $pustaha->author = $simsdm->getEmployee($this->user_info['user_id'])->nidn;
        $pustaha->research_full = null;

        $pustaha_items = new Collection();
        $pustaha_item = new PustahaItem();
        $pustaha_item->item_external = null;
        $pustaha_items->push($pustaha_item);
        $researches = $simpel->getResearches($pustaha->author);

        $auth = UserAuth::where('username',$this->user_info['user_id'])->first();

        if(empty($auth)) $auth = "";
        else $auth = $auth->auth_type;

        $diseminasi = Diseminasi::where('pustaha_id', $id)->first();
        if(!empty($diseminasi)){ $disabled = 'disabled'; $upd_mode = 'edit'; }
        else $diseminasi = new Diseminasi();

        $approval1 = $pustaha->approval()->get();
        $approvales = new Collection();
        foreach ($approval1 as $approv) {
            $app = new Collection();
            $status = $approv->statusCode()->first();
            $app->code = $approv->approval_status;
            $app->approval_annotation = $approv->approval_annotation;
            $app->approval_status = $status->code_description;
            $app->created_at = $approv->created_at;
            $approvales->push($app);
        }

        $edit = false;

        $approval = $pustaha->approval()->orderBy('item','desc')->first();

        if ($approval->approval_status =='RJ:LP') $edit = true;

        return view('pustaha.diseminasi-form', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'pustaha',
            'pustaha_items',
            'researches',
            'auth',
            'diseminasi',
            'approvales',
            'edit'
        ));
    }

    public function diseminasiStore(StoreDisseminationRequest $request){
        $pustaha = Pustaha::find($request->pustaha_id);

        if ($pustaha->pustaha_type == 'BUKU')
            $path = 'book';
        elseif ($pustaha->pustaha_type == 'JURNAL-N' || $pustaha->pustaha_type == 'JURNAL-I')
            $path = 'journal';
        elseif ($pustaha->pustaha_type == 'PROSIDING-N' || $pustaha->pustaha_type == 'PROSIDING-I')
            $path = 'proceeding';
        elseif ($pustaha->pustaha_type == 'HKI')
            $path = 'hki';
        elseif ($pustaha->pustaha_type == 'PATEN')
            $path = 'patent';

        $pustaha->diseminasi = Diseminasi::where('pustaha_id',$pustaha->id)->first();

        if(empty($pustaha->diseminasi)){
            $pustaha->diseminasi = new Diseminasi();            
        }

        if (! is_null($request->file('file_dissemination_ori'))){
            $pustaha->diseminasi->file_dissemination_ori = $request->file('file_dissemination_ori')->getClientOriginalName();
            $pustaha->diseminasi->file_dissemination = sha1(bcrypt($pustaha->diseminasi->file_dissemination_ori)) . '.' . $request->file('file_dissemination_ori')->getClientOriginalExtension();
        }

        if (! is_null($request->file('file_iptek_ori'))){
            $pustaha->diseminasi->file_iptek_ori = $request->file('file_iptek_ori')->getClientOriginalName();
            $pustaha->diseminasi->file_iptek = sha1(bcrypt($pustaha->diseminasi->file_iptek_ori)) . '.' . $request->file('file_iptek_ori')->getClientOriginalExtension();
        }

        if (! is_null($request->file('file_presentation_ori'))){
            $pustaha->diseminasi->file_presentation_ori = $request->file('file_presentation_ori')->getClientOriginalName();
            $pustaha->diseminasi->file_presentation = sha1(bcrypt($pustaha->diseminasi->file_presentation_ori)) . '.' . $request->file('file_presentation_ori')->getClientOriginalExtension();
        }

        if (! is_null($request->file('file_poster_ori'))){
            $pustaha->diseminasi->file_poster_ori = $request->file('file_poster_ori')->getClientOriginalName();
            $pustaha->diseminasi->file_poster = sha1(bcrypt($pustaha->diseminasi->file_poster_ori)) . '.' . $request->file('file_poster_ori')->getClientOriginalExtension();
        }

        $last_item = $pustaha->approval()->orderBy('id', 'desc')->first();

        $pustaha->approval = new Approval();
        $pustaha->approval->item = $last_item->item + 1;

        if($request->upd_mode == 'edit'){
            $pustaha->approval->approval_status = 'UPD';
            $pustaha->approval->approval_annotation = 'Updated Dissemination';
        }else{
            $pustaha->approval->approval_status = 'SBD';
            $pustaha->approval->approval_annotation = 'Submitted Dissemination';
        }

        $pustaha->approval->created_by = Auth::user()->user_id;

        DB::transaction(function () use ($pustaha, $path, $request)
        {
            $saved_dissemination = $pustaha->diseminasi()->save($pustaha->diseminasi);
            if ($saved_dissemination)
            {
                $pustaha->approval()->save($pustaha->approval);
                $path = $path . '/' . $pustaha->id . '/dissemination';

                $path = Storage::url('upload/' . Auth::user()->user_id . '/' . $path . '/');

                if (! is_null($request->file('file_dissemination_ori')))
                    $request->file('file_dissemination_ori')->storeAs($path, $pustaha->diseminasi->file_dissemination);
                
                if (! is_null($request->file('file_iptek_ori')))
                    $request->file('file_iptek_ori')->storeAs($path, $pustaha->diseminasi->file_iptek);

                if (! is_null($request->file('file_presentation_ori')))
                    $request->file('file_presentation_ori')->storeAs($path, $pustaha->diseminasi->file_presentation);

                if (! is_null($request->file('file_poster_ori')))
                    $request->file('file_poster_ori')->storeAs($path, $pustaha->diseminasi->file_poster);                    
            }
        });

        $request->session()->flash('alert-success', 'Permohonan disimenasi berhasil diajukan');

        return redirect()->intended('/');
    }

    public function diseminasiEdit()
    {
        $id = Input::get('id');
        $pustaha = Pustaha::find($id);
        
        if (!empty($pustaha))
        {
            if($pustaha->pustaha_type != 'PROSIDING-N' && $pustaha->pustaha_type != 'PROSIDING-I')
                return abort('404');
        }else{
            return abort('404');
        }

        array_push($this->css['pages'], 'kartik-v/bootstrap-fileinput/css/fileinput.min.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['scripts'], 'kartik-v/bootstrap-fileinput/js/fileinput.min.js');

        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'diseminasi';
        $action_url = 'pustahas/diseminasi';
        $page_title = 'Permohonan Diseminasi';
        $disabled = '';

        $simsdm = new Simsdm();
        $simpel = new Research();
        $pustaha->author = $simsdm->getEmployee($this->user_info['user_id'])->nidn;
        $pustaha->research_full = null;

        $pustaha_items = new Collection();
        $pustaha_item = new PustahaItem();
        $pustaha_item->item_external = null;
        $pustaha_items->push($pustaha_item);
        $researches = $simpel->getResearches($pustaha->author);

        $auth = UserAuth::where('username',$this->user_info['user_id'])->first();

        if(empty($auth)) $auth = "";
        else $auth = $auth->auth_type;

        $diseminasi = Diseminasi::where('pustaha_id', $id)->first();
        if(!empty($diseminasi)){ $upd_mode = 'edit'; }
        else $diseminasi = new Diseminasi();

        $approval1 = $pustaha->approval()->get();
        $approvales = new Collection();
        foreach ($approval1 as $approv) {
            $app = new Collection();
            $status = $approv->statusCode()->first();
            $app->code = $approv->approval_status;
            $app->approval_annotation = $approv->approval_annotation;
            $app->approval_status = $status->code_description;
            $app->created_at = $approv->created_at;
            $approvales->push($app);
        }

        return view('pustaha.diseminasi-form', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'pustaha',
            'pustaha_items',
            'researches',
            'auth',
            'diseminasi',
            'approvales'
        ));
    }

    public function insentif()
    {
        array_push($this->css['pages'], 'kartik-v/bootstrap-fileinput/css/fileinput.min.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['scripts'], 'kartik-v/bootstrap-fileinput/js/fileinput.min.js');

        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'create';
        $action_url = 'pustahas/insentif';
        $page_title = 'Permohonan insentif';
        $disabled = '';

        $pustaha = new Pustaha();
        $simsdm = new Simsdm();
        $simpel = new Research();
        $pustaha->author = $simsdm->getEmployee($this->user_info['user_id'])->nidn;
        $pustaha->research_full = null;

        $pustaha_items = new Collection();
        $pustaha_item = new PustahaItem();
        $pustaha_item->item_external = null;
        $pustaha_items->push($pustaha_item);
        $researches = $simpel->getResearches($pustaha->author);

        $auth = UserAuth::where('username',$this->user_info['user_id'])->first();

        if(empty($auth)) $auth = "";
        else $auth = $auth->auth_type;

        return view('pustaha.insentif-form', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'pustaha',
            'pustaha_items',
            'researches',
            'auth'
        ));
    }

    public function destroy()
    {
        $id = Input::get('id');
        $pustaha = Pustaha::find($id);
        if(empty($pustaha))
        {
            return abort('404');
        }
        $saved = $pustaha->approval()->delete();
        $saved = $pustaha->pustahaItem()->delete();
        $saved = $pustaha->diseminasi()->delete();
        $saved = $pustaha->delete();
        if($saved)
            session()->flash('alert-success', 'Pustaha berhasil dihapus');
        else
            session()->flash('alert-danger', 'Terjadi kesalahan pada sistem, Pustaha gagal dihapus');

        return redirect()->intended('/');
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
            elseif ($pustaha->pustaha_type == 'PROSIDING-N' || $pustaha->pustaha_type == 'PROSIDING-I')
                $path = 'proceeding';
            elseif ($pustaha->pustaha_type == 'HKI')
                $path = 'hki';
            elseif ($pustaha->pustaha_type == 'PATEN')
                $path = 'patent';
            $path = $path . '/' . $pustaha->id;
            $path = Storage::url('upload/' . $pustaha->author . '/' . $path . '/');


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

    public function diseminasiDownload(){
        $input = Input::get();

        if (! isset($input['id']) || ! isset($input['type']))
        {
            return abort('404');
        }

        $diseminasi = Diseminasi::find($input['id']);
        $pustaha = Pustaha::find($diseminasi->pustaha_id);

        if (! is_null($diseminasi))
        {
            if ($pustaha->pustaha_type == 'BUKU')
                $path = 'book';
            elseif ($pustaha->pustaha_type == 'JURNAL-N' || $pustaha->pustaha_type == 'JURNAL-I')
                $path = 'journal';
            elseif ($pustaha->pustaha_type == 'PROSIDING-N' || $pustaha->pustaha_type == 'PROSIDING-I')
                $path = 'proceeding';
            elseif ($pustaha->pustaha_type == 'HKI')
                $path = 'hki';
            elseif ($pustaha->pustaha_type == 'PATEN')
                $path = 'patent';
            $path = $path . '/' . $pustaha->id;
            $path = Storage::url('upload/' . $pustaha->author . '/' . $path . '/dissemination');


            if ($input['type'] == '1')
            {
                $path = storage_path() . '/app' . $path . '/' . $diseminasi->file_dissemination;
                return response()->download($path, $diseminasi->file_dissemination_ori);
            } elseif ($input['type'] == '2')
            {
                $path = storage_path() . '/app' . $path . '/' . $diseminasi->file_iptek;
                return response()->download($path, $diseminasi->file_iptek_ori);
            } elseif ($input['type'] == '3')
            {
                $path = storage_path() . '/app' . $path . '/' . $diseminasi->file_presentation;
                return response()->download($path, $diseminasi->file_presentation_ori);
            } elseif ($input['type'] == '4')
            {
                $path = storage_path() . '/app' . $path . '/' . $diseminasi->file_poster;
                return response()->download($path, $diseminasi->file_poster_ori);
            }
        } else
        {
            return abort('404');
        }
    }

    public function getAjax()
    {
        $auth = UserAuth::where('username',Auth::user()->user_id)->first();

        if(!isset($auth)){
            $pustahas = Pustaha::where('author', Auth::user()->user_id)->get();
        }else{
            if($auth->auth_type=="SU"){
                $pustahas = Pustaha::all();
            }elseif($auth->auth_type=="WR3" || $auth->auth_type=="OPEL"){
                $pustahas = Pustaha::where('author', Auth::user()->user_id)->get();
            }else{
                $pustahas = new Pustaha();
            }
        }

        if($pustahas->isEmpty()){
            $data['data'] = [];
        }else{
            $data = [];

            $i = 0;
            foreach ($pustahas as $pustaha)
            {
                $approval = Approval::where('pustaha_id', $pustaha->id)->orderBy('id', 'desc')->first();
                $status = $approval->statusCode()->first();

                $data['data'][$i][0] = $pustaha->id;
                $data['data'][$i][1] = $i + 1;
                
                if($pustaha->pustaha_type == "JURNAL-N"){
                    $pustaha_type = 'JURNAL NASIONAL';
                }
                else if($pustaha->pustaha_type == "JURNAL-I"){
                    $pustaha_type = 'JURNAL INTERNASIONAL';
                }
                else if($pustaha->pustaha_type == "PROSIDING-N"){
                    $pustaha_type = 'PROSIDING NASIONAL';
                }
                else if($pustaha->pustaha_type == "PROSIDING-I"){
                    $pustaha_type = 'PROSIDING INTERNASIONAL';
                }
                else{
                    $pustaha_type = $pustaha->pustaha_type;   
                }

                $data['data'][$i][2] = $pustaha_type;
                $data['data'][$i][3] = $pustaha->title;
                $data['data'][$i][4] = $pustaha->pustaha_date;
                if (! empty($pustaha->isbn_issn))
                    $data['data'][$i][5] = $pustaha->isbn_issn;
                else
                    $data['data'][$i][5] = $pustaha->registration_no;
                $data['data'][$i][6] = $status->code_description;

                $diseminasi = '<a data-toggle="tooltip" data-placement="top" title="Lihat"><button class="btn btn-theme btn-sm rounded diseminasi">Diseminasi</button></a>';
                $insentive = '<a data-toggle="tooltip" data-placement="top" data-original-title="Delete"><button class="btn btn-lilac btn-sm rounded insentif">Insentif</button></a>';
                if($pustaha->pustaha_type == "PROSIDING-N" || $pustaha->pustaha_type == "PROSIDING-I"){
                    $data['data'][$i][7] = $diseminasi.$insentive;
                }else{
                    $data['data'][$i][7] = $insentive;
                }

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
        }

        
        $data = json_encode($data, JSON_PRETTY_PRINT);
        return response($data, 200)->header('Content-Type', 'application/json');
    }

    private function assignBook($request)
    {
        $ret = new \stdClass();

        if(empty($request->id)){
            $ret->pustaha = new Pustaha();
        }else{
            $ret->pustaha = Pustaha::find($request->id);
        }

        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->user_id;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->city = $request->city;
        $ret->pustaha->country = $request->country;
        $ret->pustaha->publisher = $request->publisher;
        $ret->pustaha->editor = $request->editor;
        $ret->pustaha->issue = $request->issue;
        $ret->pustaha->isbn_issn = $request->isbn_issn;
        if(empty($request->id))
        {
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
            // $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        }

        if(!empty($request->id) && !empty($request->file('file_name_ori'))){
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
        }
        // else if(!empty($request->id) && !empty($request->file('file_claim_request_ori'))){
        //     $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_claim_accomodation_ori'))){
        //     $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_certification_ori'))){
        //     $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
        //     $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        // }

        $ret->pustaha_items = new Collection();
        foreach ($request->item_username_display as $key => $item)
        {
            $pustaha_item = new PustahaItem();
            if ($request->item_username[$key] == '')
            {
                $pustaha_item->item_name = $request->item_name[$key];
                $pustaha_item->item_affiliation = $request->item_affiliation[$key];
            } else
            {
                $pustaha_item->item_username = $request->item_username[$key];
            }
            $ret->pustaha_items->push($pustaha_item);
        }

        return $ret;
    }

    private function assignJournal($request)
    {
        $ret = new \stdClass();
        if(empty($request->id)){
            $ret->pustaha = new Pustaha();
        }else{
            $ret->pustaha = Pustaha::find($request->id);
        }
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->user_id;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->name = $request->name;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->pages = $request->pages;
        $ret->pustaha->volume = $request->volume;
        $ret->pustaha->issue = $request->issue;
        $ret->pustaha->isbn_issn = $request->isbn_issn;
        $ret->pustaha->url_address = $request->url_address;

        if(empty($request->id))
        {
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
            // $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        }

        if(!empty($request->id) && !empty($request->file('file_name_ori'))){
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
        }
        // else if(!empty($request->id) && !empty($request->file('file_claim_request_ori'))){
        //     $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_claim_accomodation_ori'))){
        //     $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_certification_ori'))){
        //     $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
        //     $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        // }

        $ret->pustaha_items = new Collection();
        foreach ($request->item_username_display as $key => $item)
        {
            $pustaha_item = new PustahaItem();
            if ($request->item_username[$key] == '')
            {
                $pustaha_item->item_name = $request->item_name[$key];
                $pustaha_item->item_affiliation = $request->item_affiliation[$key];
            } else
            {
                $pustaha_item->item_username = $request->item_username[$key];
            }
            $ret->pustaha_items->push($pustaha_item);
        }

        return $ret;
    }

    private function assignProceeding($request)
    {
        $ret = new \stdClass();

        if(empty($request->id)){
            $ret->pustaha = new Pustaha();
        }else{
            $ret->pustaha = Pustaha::find($request->id);
        }

        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->user_id;
        $ret->pustaha->publisher = $request->publisher;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->name = $request->name;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->city = $request->city;
        $ret->pustaha->country = $request->country;
        $ret->pustaha->pages = $request->pages;
        $ret->pustaha->isbn_issn = $request->isbn_issn;
        $ret->pustaha->url_address = $request->url_address;

        if(empty($request->id))
        {
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
            // $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        }

        if(!empty($request->id) && !empty($request->file('file_name_ori'))){
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
        }
        // else if(!empty($request->id) && !empty($request->file('file_claim_request_ori'))){
        //     $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_claim_accomodation_ori'))){
        //     $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_certification_ori'))){
        //     $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
        //     $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        // }

        $ret->pustaha_items = new Collection();
        foreach ($request->item_username_display as $key => $item)
        {
            $pustaha_item = new PustahaItem();
            if ($request->item_username[$key] == '')
            {
                $pustaha_item->item_name = $request->item_name[$key];
                $pustaha_item->item_affiliation = $request->item_affiliation[$key];
            } else
            {
                $pustaha_item->item_username = $request->item_username[$key];
            }
            $ret->pustaha_items->push($pustaha_item);
        }

        return $ret;
    }

    private function assignHki($request)
    {
        $ret = new \stdClass();
        if(empty($request->id)){
            $ret->pustaha = new Pustaha();
        }else{
            $ret->pustaha = Pustaha::find($request->id);
        }
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->user_id;
        $ret->pustaha->propose_no = $request->propose_no;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->creator_name = $request->creator_name;
        $ret->pustaha->creator_address = $request->creator_name;
        $ret->pustaha->creator_citizenship = $request->creator_name;
        $ret->pustaha->owner_name = $request->owner_name;
        $ret->pustaha->owner_address = $request->owner_address;
        $ret->pustaha->owner_citizenship = $request->owner_citizenship;
        $ret->pustaha->creation_type = $request->creation_type;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->announcement_date = $request->announcement_date;
        $ret->pustaha->announcement_place = $request->announcement_place;
        $ret->pustaha->protection_period = $request->protection_period;
        $ret->pustaha->registration_no = $request->registration_no;

        if(empty($request->id))
        {
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
            // $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        }

        if(!empty($request->id) && !empty($request->file('file_name_ori'))){
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
        }
        // else if(!empty($request->id) && !empty($request->file('file_claim_request_ori'))){
        //     $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_claim_accomodation_ori'))){
        //     $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_certification_ori'))){
        //     $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
        //     $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        // }

        return $ret;
    }

    private function assignPatent($request)
    {
        $ret = new \stdClass();
        if(empty($request->id)){
            $ret->pustaha = new Pustaha();
        }else{
            $ret->pustaha = Pustaha::find($request->id);
        }
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->user_id;
        $ret->pustaha->propose_no = $request->propose_no;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->creator_name = $request->creator_name;
        $ret->pustaha->creator_address = $request->creator_name;
        $ret->pustaha->creator_citizenship = $request->creator_name;
        $ret->pustaha->owner_name = $request->owner_name;
        $ret->pustaha->owner_address = $request->owner_address;
        $ret->pustaha->owner_citizenship = $request->owner_citizenship;
        $ret->pustaha->creation_type = $request->creation_type;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->announcement_date = $request->announcement_date;
        $ret->pustaha->announcement_place = $request->announcement_place;
        $ret->pustaha->protection_period = $request->protection_period;
        $ret->pustaha->registration_no = $request->registration_no;

        if(empty($request->id))
        {
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
            // $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
            // $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
            // $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        }

        if(!empty($request->id) && !empty($request->file('file_name_ori'))){
            $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
            $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();
        }
        // else if(!empty($request->id) && !empty($request->file('file_claim_request_ori'))){
        //     $ret->pustaha->file_claim_request_ori = $request->file('file_claim_request_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_request = sha1(bcrypt($ret->pustaha->file_claim_request_ori)) . '.' . $request->file('file_claim_request_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_claim_accomodation_ori'))){
        //     $ret->pustaha->file_claim_accomodation_ori = $request->file('file_claim_accomodation_ori')->getClientOriginalName();
        //     $ret->pustaha->file_claim_accomodation = sha1(bcrypt($ret->pustaha->file_claim_accomodation_ori)) . '.' . $request->file('file_claim_accomodation_ori')->getClientOriginalExtension();
        // }else if(!empty($request->id) && !empty($request->file('file_certification_ori'))){
        //     $ret->pustaha->file_certification_ori = $request->file('file_certification_ori')->getClientOriginalName();
        //     $ret->pustaha->file_certification = sha1(bcrypt($ret->pustaha->file_certification_ori)) . '.' . $request->file('file_certification_ori')->getClientOriginalExtension();
        // }

        return $ret;
    }

    public function searchResearch()
    {
        $input = Input::get();
        $res = new Research();
        $simsdm = new Simsdm();
        $auths = null;
        $user_auth = UserAuth::where('username',$this->user_info['user_id'])->get();
        $research = $res->searchResearchTitle($input['query']);

        $results = new Collection();
        
        if(empty($research->data)){
            $result = new \stdClass();
            $result->research_id = 0;
            $result->label = 'Research is not found';
            $results->push($result);
        }else{
            foreach ($research->data as $rsc)
            {
                $result = new \stdClass();
                $result->research_id = $rsc->id;
                $user = $simsdm->searchEmployee($rsc->author,1);
                $user = $user->data[0];
                $result->label = 'Author: ' . $user->full_name . ', Judul Penelitian: ' . $rsc->title;
                $results->push($result);
            }    
        }
        
        $results = json_encode($results, JSON_PRETTY_PRINT);

        return response($results, 200)->header('Content-Type', 'application/json');
    }

    public function reportList(){
        $page_title = 'Report Pustaha';

        return view('pustaha.pustaha-report', compact('page_title'));
    }
}