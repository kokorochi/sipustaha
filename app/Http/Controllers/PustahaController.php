<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePustahaRequest;
use App\Pustaha;
use App\PustahaItem;
use App\Simsdm;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use View;
use parinpan\fanjwt\libs\JWTAuth;

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
        $pustaha->author = $this->user_info['username'];

        $pustaha_items = new Collection();
        $pustaha_item = new PustahaItem();
        $pustaha_item->item_external = null;
        $pustaha_items->push($pustaha_item);

        return view('pustaha.pustaha-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'pustaha',
            'pustaha_items'
        ));
    }

    public function store(StorePustahaRequest $request)
    {
        if ($request->pustaha_type == 'BUKU')
        {
            $path = 'book';
            $var_pustaha = $this->assignBook($request);
        } elseif ($request->pustaha_type == 'JURNAL')
        {
            $path = 'journal';
            $var_pustaha = $this->assignJournal($request);
        } elseif ($request->pustaha_type == 'PROSIDING')
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

        DB::transaction(function () use ($var_pustaha, $path, $request)
        {
            $saved_pustaha = $var_pustaha->pustaha->save();
            if ($saved_pustaha)
            {
                if (isset($var_pustaha->pustaha_items))
                {
                    $var_pustaha->pustaha->pustahaItem()->saveMany($var_pustaha->pustaha_items);
                }

                $path = Storage::url('upload/' . Auth::user()->username . '/' . $path . '/');
                $request->file('file_name_ori')->storeAs($path, $var_pustaha->pustaha->file_name);
            }
        });

        return redirect()->intended('/');
    }

    public function getAjax()
    {
        $pustahas = Pustaha::where('author', Auth::user()->username)->get();

        $data = [];

        $i = 0;
        foreach ($pustahas as $pustaha)
        {
            if ($pustaha->pustaha_type == 'BUKU' ||
                $pustaha->pustaha_type == 'JURNAL' ||
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
                        if(!empty($full_name))
                            $co_authors = $co_authors . $full_name . '; ';
                    } else
                    {
                        $co_authors = $co_authors . $pustaha_item->name . '; ';
                    }
                }
            }

            $data['data'][$i][0] = $pustaha->id;
            $data['data'][$i][1] = $i + 1;
            $data['data'][$i][2] = $pustaha->pustaha_type;
            $data['data'][$i][3] = $pustaha->title;
            $data['data'][$i][4] = $pustaha->pustaha_date;
            if(!empty($pustaha->isbn_issn))
                $data['data'][$i][5] = $pustaha->isbn_issn;
            else
                $data['data'][$i][5] = $pustaha->registration_no;

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

    private function assignBook($request)
    {
        $ret = new \stdClass();
        $ret->pustaha = new Pustaha();
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->username;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->city = $request->city;
        $ret->pustaha->country = $request->country;
        $ret->pustaha->publisher = $request->publisher;
        $ret->pustaha->editor = $request->editor;
        $ret->pustaha->issue = $request->issue;
        $ret->pustaha->isbn_issn = $request->isbn_issn;
        $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
        $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();

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
        $ret->pustaha = new Pustaha();
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->username;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->name = $request->name;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->pages = $request->pages;
        $ret->pustaha->volume = $request->volume;
        $ret->pustaha->issue = $request->issue;
        $ret->pustaha->isbn_issn = $request->isbn_issn;
        $ret->pustaha->url_address = $request->url_address;
        $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
        $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();

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
        $ret->pustaha = new Pustaha();
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->username;
        $ret->pustaha->publisher = $request->publisher;
        $ret->pustaha->title = $request->title;
        $ret->pustaha->name = $request->name;
        $ret->pustaha->pustaha_date = date('Y-m-d', strtotime($request->pustaha_date));
        $ret->pustaha->city = $request->city;
        $ret->pustaha->country = $request->country;
        $ret->pustaha->pages = $request->pages;
        $ret->pustaha->isbn_issn = $request->isbn_issn;
        $ret->pustaha->url_address = $request->url_address;
        $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
        $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();

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
        $ret->pustaha = new Pustaha();
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->username;
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
        $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
        $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();

        return $ret;
    }

    private function assignPatent($request)
    {
        $ret = new \stdClass();
        $ret->pustaha = new Pustaha();
        $ret->pustaha->pustaha_type = $request->pustaha_type;
        $ret->pustaha->author = Auth::user()->username;
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
        $ret->pustaha->file_name_ori = $request->file('file_name_ori')->getClientOriginalName();
        $ret->pustaha->file_name = sha1(bcrypt($ret->pustaha->file_name_ori)) . '.' . $request->file('file_name_ori')->getClientOriginalExtension();

        return $ret;
    }
}
