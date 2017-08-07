<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePustahaRequest extends FormRequest {
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
        if ($this->input('pustaha_type') == 'BUKU')
        {
            $rules = [
                'title'                       => 'required',
                'pustaha_date'                => 'required',
                'city'                        => 'required|max:100',
                'country'                     => 'required|max:100',
                'publisher'                   => 'required|max:191',
                'editor'                      => 'required|max:191',
                'issue'                       => 'required|max:191',
                'isbn_issn'                   => 'required|max:191',
            ];
        } elseif ($this->input('pustaha_type') == 'JURNAL-N' || $this->input('pustaha_type') == 'JURNAL-I')
        {
            $rules = [
                'title'                       => 'required',
                'name'                        => 'required',
                'pustaha_date'                => 'required',
                'pages'                       => 'required|integer|max:99999',
                'volume'                      => 'required|max:191',
                'issue'                       => 'required|max:191',
                'isbn_issn'                   => 'required|max:191',
                'url_address'                 => 'required|max:191',
            ];
        } elseif ($this->input('pustaha_type') == 'PROSIDING')
        {
            $rules = [
                'publisher'                   => 'required|max:191',
                'title'                       => 'required',
                'name'                        => 'required',
                'pustaha_date'                => 'required',
                'city'                        => 'required|max:100',
                'country'                     => 'required|max:100',
                'pages'                       => 'required|integer|max:99999',
                'isbn_issn'                   => 'required|max:191',
                'url_address'                 => 'required|max:191',
            ];
        } elseif ($this->input('pustaha_type') == 'HKI' || $this->input('pustaha_type') == 'PATEN')
        {
            $rules = [
                'propose_no'                  => 'required|max:191',
                'pustaha_date'                => 'required',
                'creator_name'                => 'required|max:191',
                'creator_address'             => 'required',
                'creator_citizenship'         => 'required|max:191',
                'owner_name'                  => 'required|max:191',
                'owner_address'               => 'required',
                'owner_citizenship'           => 'required|max:191',
                'creation_type'               => 'required|max:191',
                'title'                       => 'required',
                'announcement_date'           => 'required',
                'announcement_place'          => 'required|max:191',
                'protection_period'           => 'required|max:191',
                'registration_no'             => 'required|max:191',
            ];
        }
        if(!$this->input('_method')){
            $rules = array_add($rules, 'file_name_ori', 'required|mimetypes:application/pdf');
            $rules = array_add($rules, 'file_claim_request_ori', 'required|mimetypes:application/pdf');
            $rules = array_add($rules, 'file_claim_accomodation_ori', 'required|mimetypes:application/pdf');
            $rules = array_add($rules, 'file_certification_ori', 'required|mimetypes:application/pdf');
        }
        return $rules;
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
     
        if ($this->input('pustaha_type') == 'BUKU' ||
            $this->input('pustaha_type') == 'JURNAL-N' || $this->input('pustaha_type') == 'JURNAL-I' ||
            $this->input('pustaha_type') == 'PROSIDING'
        )
        {
            foreach ($this->input('item_username_display') as $key => $item)
            {
                $line_item = $key + 1;
                if ($this->input('item_username_display')[$key] == '')
                {
                    if ($this->input('item_name')[$key] == '' ||
                        $this->input('item_affiliation')[$key] == ''
                    )
                    {
                        $ret[] = 'Data Penulis berikutnya / Co-Author tidak lengkap! [' . $line_item . ']';
                    }
                } else
                {
                    if ($this->input('item_username')[$key] == '')
                    {
                        $ret[] = 'Data Penulis berikutnya / Co-Author tidak lengkap! [' . $line_item . ']';
                    }
                }
            }
        }

        return $ret;
    }
}
