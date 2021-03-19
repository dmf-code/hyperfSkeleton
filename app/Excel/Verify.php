<?php


namespace App\Excel;


trait Verify
{
    /**
     * 校验数据不能为空
     * @param $col
     * @return bool|string
     */
    public function checkNotEmpty($col)
    {
        if (empty($col)) {
            return '数据不能为空';
        }

        return true;
    }

    public function checkMobile($col)
    {
        if (!preg_match("/^1[34578]\d{9}$/", $col)) {
            return "请填写正确手机号";
        }

        return true;
    }

    /**
     * @param $col
     * @param $args ['max_len' =>  12]
     * @return bool|string
     */
    public function checkMoreThan($col, $args)
    {
        if (mb_strlen($col) > $args['max_len']) {
            return "该字段不能超过 {$args['max_len']} 个字";
        }

        return true;
    }

    /**
     * @param $col
     * @param $args ['label' => 'key 标签', 'key' => ['1' => 1]]
     * @return bool|string
     */
    public function checkKeyExists($col, $args)
    {
        if (!array_key_exists($col, $args['key'])) {
            return "{$args['label']} 不存在";
        }

        return true;
    }

    /**
     * @param $col
     * @param $args ['label' => '导入的head名称']
     * @return string
     */
    public function checkNumber($col, $args)
    {
        if (!is_numeric($col)) {
            return "{$args['label']} 不是数字";
        }
        return true;
    }
}