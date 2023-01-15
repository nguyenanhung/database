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
 * @package           nguyenanhung\MyDatabase\Model
 * @author            713uk13m <dev@nguyenanhung.com>
 * @copyright         713uk13m <dev@nguyenanhung.com>
 * @since             2021-09-22
 * @last_updated      2021-09-22
 * @version           3.0.4
 */
trait Helper
{
    /**
     * Function preparePaging
     *
     * @param int $pageIndex
     * @param int $pageSize
     *
     * @see      https://github.com/nguyenanhung/database/blob/master/src/Model/Helper.php
     * @see      https://laravel.com/docs/6.x/queries#ordering-grouping-limit-and-offset
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/21/2021 23:24
     */
    public function preparePaging($pageIndex = 1, $pageSize = 10)
    {
        if ($pageIndex !== 0) {
            if ($pageIndex <= 0 || empty($pageIndex)) {
                $pageIndex = 1;
            }
            $offset = ($pageIndex - 1) * $pageSize;
        } else {
            $offset = $pageIndex;
        }

        return array('offset' => $offset, 'limit' => $pageSize);
    }

    /**
     * Function prepareOptionFormat
     *
     * @param mixed $options
     *
     * @see      https://github.com/nguyenanhung/database/blob/master/src/Model/Helper.php
     *
     * @return string|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/22/2021 22:22
     */
    protected function prepareOptionFormat($options = array())
    {
        if (isset($options['format']) && is_string($options['format'])) {
            $format = strtolower($options['format']);
        } elseif (is_string($options)) {
            $format = strtolower($options);
        } else {
            $format = null;
        }

        return $format;
    }

    /**
     * Function formatSelectFieldStringToArray
     *
     * @param string $selectField String danh sác các cột cần lấy ra
     *
     * @see      https://github.com/nguyenanhung/database/blob/master/src/Model/Helper.php
     * @see      https://laravel.com/docs/6.x/queries#selects
     *
     * @return array|string|string[]
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/21/2021 10:57
     */
    protected function formatSelectFieldStringToArray($selectField = '')
    {
        if (is_string($selectField)) {
            if ($selectField === '*') {
                return array('*');
            }
            $listSelectField = explode(',', $selectField);
            $select = array();
            foreach ($listSelectField as $field) {
                $field = trim($field);
                if (!empty($field)) {
                    $select[] = trim($field);
                }
            }
            if (empty($select)) {
                return array('*');
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
     * @see      https://github.com/nguyenanhung/database/blob/master/src/Model/Helper.php
     * @see      https://laravel.com/docs/6.x/queries#selects
     *
     * @return array|string|string[]
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/21/2021 12:12
     */
    protected function prepareFormatSelectField($selectField = array())
    {
        if ($selectField === null) {
            return array('*');
        }

        // Format: If Select Field is String
        if (is_string($selectField)) {
            return $this->formatSelectFieldStringToArray($selectField);
        }

        // Format: If Select Field is Array
        if (is_array($selectField) && !empty($selectField) && $selectField[0] !== '*') {
            $listFirstField = explode(',', $selectField[0]);
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
     * Function prepareWhereAndFieldStatement
     *
     * @param \Illuminate\Database\Query\Builder $builder Class Query Builder
     * @param string|array                       $wheres  Mảng hoặc giá trị dữ liệu cần so sánh
     * @param string                             $fields  Column cần so sánh
     * @param mixed                              $options Mảng dữ liệu các cấu hình tùy chọn
     *                                                    example $options = [
     *                                                    'format' => null,
     *                                                    'orderBy => [
     *                                                    'id' => 'desc'
     *                                                    ]
     *                                                    ];
     *
     * @see      https://github.com/nguyenanhung/database/blob/master/src/Model/Helper.php
     * @see      https://laravel.com/docs/6.x/queries
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/22/2021 02:38
     */
    protected function prepareWhereAndFieldStatement(Builder $builder, $wheres, $fields, $options = null)
    {
        if (!empty($wheres)) {
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
        }

        if ($options !== null) {
            // Case có cả Limit  và Offset -> active phân trang
            if (isset($options['limit'], $options['offset']) && $options['limit'] > 0) {
                $page = $this->preparePaging($options['offset'], $options['limit']);
                $builder->offset($page['offset'])->limit($page['limit']);
            }

            // Case chỉ có Limit
            if ((isset($options['limit']) && $options['limit'] > 0) && !isset($options['offset'])) {
                $builder->limit($options['limit']);
            }

            // Sắp xếp dữ liệu đổ ra dựa vào Option Order By
            if (isset($options['orderBy']) && is_array($options['orderBy'])) {
                foreach ($options['orderBy'] as $column => $direction) {
                    $builder->orderBy($column, $direction);
                }
            }

            // Sắp xếp dữ liệu đổ ra ngẫu nhiên nếu như Option Order By ghi nhận giá trị random
            if (isset($options['orderBy']) && is_string($options['orderBy']) && strtolower($options['orderBy']) === 'random') {
                $builder->inRandomOrder();
            }

            // Group Query
            if (isset($options['groupBy']) && !empty($options['groupBy'])) {
                $builder->groupBy($options['groupBy']);
            }
        }

        return $builder;
    }

    protected function prepareJoinStatement(Builder $builder)
    {
        if (!empty($this->joins) && is_array($this->joins)) {
            foreach ($this->joins as $join) {
                if (isset($join['table'], $join['first'], $join['operator'], $join['second'])) {
                    // Tiến hành join vào các bảng để lấy CSDL
                    if (isset($join['type'])) {
                        $builder->join($join['table'], $join['first'], $join['operator'], $join['second'], $join['type']);
                    } else {
                        $builder->join($join['table'], $join['first'], $join['operator'], $join['second']);
                    }
                }
            }
        }

        return $builder;
    }
}
