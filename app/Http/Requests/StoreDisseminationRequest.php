<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Pustaha;

class StoreDisseminationRequest extends FormRequest
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
        $rules = [];

        if(!$this->input('id')){
            $rules = array_add($rules, 'file_dissemination_ori', 'required|mimetypes:application/pdf');
            $rules = array_add($rules, 'file_iptek_ori', 'required|mimetypes:application/pdf');
            $rules = array_add($rules, 'file_presentation_ori', 'required|mimetypes:application/pdf');
            $rules = array_add($rules, 'file_poster_ori', 'required|mimetypes:application/pdf');
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'file_dissemination_ori.required'     => 'Lampiran Permohonan Bantuan Diseminasi harus diisi',
            'file_dissemination_ori.mimetypes'    => 'Lampiran Permohonan Bantuan Diseminasi harus format PDF',
            'file_iptek_ori.required'             => 'File (Bukti Penyebarluasan IPTEK) harus diisi',
            'file_iptek_ori.mimetypes'            => 'File (Bukti Penyebarluasan IPTEK) harus format PDF',
            'file_presentation_ori.required'      => 'File Persentasi Seminar harus diisi',
            'file_presentation_ori.mimetypes'     => 'File Persentasi Seminar harus format PDF',
            'file_poster_ori.required'            => 'File Poster harus diisi',
            'file_poster_ori.mimetypes'           => 'File Poster harus format PDF',
        ];
    }

    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance()->after(function ($validator)
        {
            $this->after($validator);
        });
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

        $pustaha = Pustaha::find($this->input('pustaha_id'));
        
        if(empty($pustaha)){
            $ret[] = 'Pustaha tidak terdaftar';
        }

        if($pustaha->pustaha_type != 'PROSIDING-N' && $pustaha->pustaha_type != 'PROSIDING-I'){
            $ret[] = 'Pustaha ini tidak dapat diajukan permohonan bantuan diseminasi';   
        }


        return $ret;
    }
}
