<?php

namespace App\Http\Controllers;

use App\Pustaha;
use App\Simsdm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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
        return view('approval.approval-list');
    }

    public function showApproval()
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
        $action_url = '';
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
        $flow_statuses = $pustaha->flowStatus()->get();

        return view('approval.approval-detail', compact(
            'upd_mode',
            'page_title',
            'action_url',
            'disabled',
            'pustaha',
            'pustaha_items',
            'flow_statuses'
        ));
    }

    public function updateApproval()
    {

    }
}
