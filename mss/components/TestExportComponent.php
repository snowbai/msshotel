<?php

namespace mss\components;

use common\components\BaseExport;

class TestExportComponent extends BaseExport
{
    public $excel_sort_order = [
        'id' =>'id',
        'hotel_name' => '酒店名'
    ];

    public function getData()
    {
        return [
            ['id'=>1,'hotel_name'=>'你大爷1'],
            ['id'=>2,'hotel_name'=>'你大爷2'],
            ['id'=>3,'hotel_name'=>'你大爷3'],
            ['id'=>4,'hotel_name'=>'你大爷4'],
            ['id'=>5,'hotel_name'=>'你大爷5'],
        ];
    }
}
