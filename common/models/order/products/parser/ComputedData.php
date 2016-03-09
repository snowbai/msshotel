<?php

namespace common\models\order\products\parser;

/**
 * 数据格式
 * Class ComputedData
 * @package common\models\order\products\parser
 */
class ComputedData
{
    /**
     * 最终价格
     * @var
     */
    public $apply_price;

    /**
     * 产品信息数据
     * @var
     */
    public $products_info;

    /**
     * 库存信息
     * @var
     */
    public $inventory_info;
}

