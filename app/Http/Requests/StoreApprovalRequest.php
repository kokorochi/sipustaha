<?php

namespace App\Http\Requests;

use App\Incentive;
use Illuminate\Foundation\Http\FormRequest;

class StoreApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'annotation' => 'required',
            'approve_status' => 'required',
        ];

        if ($this->input('type') == 'wr3'){
            $rules = array_add($rules, 'incentive_id', 'required');
        }

        return $rules;
    }

    public function after($validator)
    {
        $check = $this->checkBeforeSave();
        if (count($check) > 0)
        {
            foreach ($check as $item)
            {
                $validator->errors()->add('alert-danger', $item);
            }
        }
    }

    private function checkBeforeSave()
    {
        $ret = [];

        if($this->input('type') == 'wr3'){
            $incentive = Incentive::where('id', $this->input('incentive_id'))->get();

            if ($incentive->isEmpty())
            {
                $ret[] = 'Incentive tidak terdapat pada tabel incentive';
            }
        }
        return $ret;
    }
}