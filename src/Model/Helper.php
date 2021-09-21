<?php
/**
 * Project database
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/21/2021
 * Time: 10:40
 */

namespace nguyenanhung\MyDatabase\Model;

use Illuminate\Database\Query\Builder;

/**
 * Trait Helper
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait Helper
{
    /**
     * Function formatSelectFieldStringToArray
     *
     * @param string $selectField String danh sác các cột cần lấy ra
     *
     * @return array|string|string[]
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/21/2021 10:57
     */
    protected function formatSelectFieldStringToArray(string $selectField = '')
    {
        if (is_string($selectField)) {
            if ($selectField === '*') {
                return ['*'];
            }
            $listSelectField = explode(',', $selectField);
            $select          = array();
            foreach ($listSelectField as $field) {
                $field = trim($field);
                if (!empty($field)) {
                    $select[] = trim($field);
                }
            }
            if (empty($select)) {
                return ['*'];
            }

            return $select;
        }

        return $selectField;
    }

    /**
     * Function prepareFormatSelectField
     *
     * @param array|string|null $selectField Mảng hoặc string danh sác các cột cần lấy ra
     *
     * @return array|string[]
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/21/2021 12:12
     */
    protected function prepareFormatSelectField($selectField = array()): array
    {
        if ($selectField === null) {
            return ['*'];
        }

        // Format: If Select Field is String
        if (is_string($selectField)) {
            return $this->formatSelectFieldStringToArray($selectField);
        }

        // Format: If Select Field is Array
        if (is_array($selectField) && !empty($selectField) && $selectField[0] !== '*') {
            $listFirstField  = explode(',', $selectField[0]);
            $countFirstField = count($listFirstField);
            if ($countFirstField > 1) {
                /**
                 * Dữ liệu đầu vào sai, thuộc dạng: ['a,b,c']
                 * Cần format lại dữ liệu này lại thành Array
                 */
                return $this->formatSelectFieldStringToArray($selectField[0]);
            }
        }


        return $selectField;
    }

    /**
     * Function prepareWhereFieldStatement
     *
     * @param \Illuminate\Database\Query\Builder $builder Class Query Builder
     * @param string|array                       $wheres  Mảng hoặc giá trị dữ liệu cần so sánh
     * @param string                             $fields  Column cần so sánh
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/22/2021 02:38
     */
    protected function prepareWhereFieldStatement(Builder $builder, $wheres, $fields): Builder
    {
        if (is_array($wheres)) {
            if (count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $builder->whereIn($value['field'], $value['value']);
                            } else {
                                $builder->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $builder->whereIn($field, $value);
                        }
                    } else {
                        $builder->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $builder->whereIn($fields, $wheres);
            }
        } else {
            $builder->where($fields, self::OPERATOR_EQUAL_TO, $wheres);
        }

        return $builder;
    }


}
