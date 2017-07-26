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
        return [
            'id_incentive' => 'required',
            'annotation' => 'required',
            'approve_status' => 'required',
        ];
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

        $incentive = Incentive::where('id', $this->input('id_incentive'))->get();

        // dd($coop_partner);
        if ($incentive->isEmpty())
        {
            $ret[] = 'Incentive tidak terdapat pada tabel incentive';

            return $ret;
        }

        return $ret;
    }
}