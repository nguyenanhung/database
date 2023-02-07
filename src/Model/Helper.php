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
use \Illuminate\Support\Collection;

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
     * Function prepareOffset
     *
     * @param $page
     * @param $size
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 48:18
     */
    public function prepareOffset($page = 1, $size = 10)
    {
        if ($page !== 0) {
            if ($page <= 0 || empty($page)) {
                $page = 1;
            }
            $start = ($page - 1) * $size;
        } else {
            $start = $page;
        }

        return (int) $start;
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
    public function prepareOptionFormat($options = array())
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
    public function formatSelectFieldStringToArray($selectField = '')
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
    public function prepareFormatSelectField($selectField = array())
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
     * Function prepareQueryStatementOptions
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @param                                    $options
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 40:06
     */
    public function prepareQueryStatementOptions(Builder $builder, $options = null)
    {
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
    public function prepareWhereAndFieldStatement(Builder $builder, $wheres, $fields, $options = null)
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

        return $this->prepareQueryStatementOptions($builder, $options);
    }

    /**
     * Function prepareSimpleWheresWithStatement
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @param                                    $wheres
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 43:10
     */
    public function prepareSimpleWheresWithStatement(Builder $builder, $wheres)
    {
        if (!empty($wheres) && is_array($wheres) && count($wheres) > 0) {
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
        }

        return $builder;
    }

    /**
     * Function prepareSimpleWheresWithOptionsStatement
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @param                                    $wheres
     * @param                                    $options
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 43:56
     */
    public function prepareSimpleWheresWithOptionsStatement(Builder $builder, $wheres, $options = null)
    {
        $builder = $this->prepareSimpleWheresWithStatement($builder, $wheres);

        return $this->prepareQueryStatementOptions($builder, $options);
    }

    /**
     * Function prepareSimpleWhereEqualToStatement
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @param                                    $wheres
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 52:43
     */
    public function prepareSimpleWhereEqualToStatement(Builder $builder, $wheres)
    {
        if (is_array($wheres)) {
            foreach ($wheres as $field => $value) {
                $builder = $this->buildOperatorEqualTo($builder, $value, $field);
            }
        }

        return $builder;
    }

    /**
     * Function prepareSimpleWhereNotEqualToStatement
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @param                                    $wheres
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 37:43
     */
    public function prepareSimpleWhereNotEqualToStatement(Builder $builder, $wheres)
    {
        if (is_array($wheres)) {
            foreach ($wheres as $field => $value) {
                $builder = $this->buildOperatorNotEqualTo($builder, $value, $field);
            }
        }

        return $builder;
    }

    /**
     * Function prepareJoinStatement
     *
     * @param \Illuminate\Database\Query\Builder $builder
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 24:41
     */
    public function prepareJoinStatement(Builder $builder)
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

    /**
     * Function formatReturnResult
     *
     * @param                                $result
     * @param                                $format
     * @param                                $loggerStatus
     *
     * @return array|\Illuminate\Support\Collection|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 55:48
     */
    public function formatReturnResult($result, $format, $loggerStatus = true)
    {
        if ($format === 'json') {
            if ($loggerStatus === true) {
                $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            }

            return $result->toJson();
        }

        if ($format === 'array') {
            if ($loggerStatus === true) {
                $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            }

            return $result->toArray();
        }

        if ($format === 'base') {
            if ($loggerStatus === true) {
                $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            }

            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function formatReturnRowsResult
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @param                                    $format
     *
     * @return array|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection|object|string|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 55:35
     */
    public function formatReturnRowsResult(Builder $builder, $format)
    {
        if ($format === 'result') {
            $result = $builder->get();
            // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $builder->first();
            // $this->logger->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }
        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }
        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }
        if (($format === 'result') && ($result->count() <= 0)) {
            return null;
        }

        return $result;
    }

    /**
     * Function bindRecursiveFromCategory
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $recursive
     * @param                                    $parentId
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 29:12
     */
    public function bindRecursiveFromCategory(Builder $db, $recursive, $parentId, $field = 'categoryId')
    {
        if (is_array($recursive) || is_object($recursive)) {
            /**
             * Xác định lấy toàn bộ tin tức ở các category con
             */
            $countSubCategory = count($recursive); // Đếm bảng ghi Category con
            if ($countSubCategory) {
                // Nếu tồn tại các category con
                $listCategory = array();
                $listCategory[] = $parentId; // Push category cha
                foreach ($recursive as $item) {
                    $itemId = is_array($item) ? $item['id'] : $item->id;
                    $listCategory[] = (int) $itemId; // Push các category con vào mảng dữ liệu
                }
                $db->whereIn($this->table . '.' . $field, $listCategory); // Lấy theo where in
            } else {
                $db->where($this->table . '.' . $field, self::OPERATOR_EQUAL_TO, $parentId); // lấy theo where
            }
        } else {
            // Trong trường hợp so sánh tuyệt đối đối với categoryId truyền vào
            $db->where($this->table . '.' . $field, self::OPERATOR_EQUAL_TO, $parentId);
        }

        return $db;
    }

    /**
     * Function filterByPrimaryId
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 07:22
     */
    public function filterByPrimaryId(Builder $db, $id, $field = 'id')
    {
        if ($id !== null) {
            if (is_array($id)) {
                $db->whereIn($this->table . '.' . $field, $id);
            } else {
                $db->where($this->table . '.' . $field, self::OPERATOR_EQUAL_TO, $id);
            }
        }

        return $db;
    }

    /**
     * Function buildOperatorEqualTo
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 18:00
     */
    public function buildOperatorEqualTo(Builder $db, $id, $field = 'id')
    {
        if ($id !== null) {
            if (is_array($id)) {
                $db->whereIn($this->table . '.' . $field, $id);
            } else {
                $db->where($this->table . '.' . $field, self::OPERATOR_EQUAL_TO, $id);
            }
        }

        return $db;
    }

    /**
     * Function buildOperatorNotEqualTo
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 29:17
     */
    public function buildOperatorNotEqualTo(Builder $db, $id, $field = 'id')
    {
        if ($id !== null) {
            if (is_array($id)) {
                $db->whereNotIn($this->table . '.' . $field, $id);
            } else {
                $db->where($this->table . '.' . $field, self::OPERATOR_NOT_EQUAL_TO, $id);
            }
        }

        return $db;
    }

    /**
     * Function buildOperatorLessThanTo
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 30:09
     */
    public function buildOperatorLessThanTo(Builder $db, $id, $field = 'id')
    {
        $db->where($this->table . '.' . $field, self::OPERATOR_LESS_THAN, $id);

        return $db;
    }

    /**
     * Function buildOperatorGreaterThanTo
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 30:05
     */
    public function buildOperatorGreaterThanTo(Builder $db, $id, $field = 'id')
    {
        $db->where($this->table . '.' . $field, self::OPERATOR_GREATER_THAN, $id);

        return $db;
    }

    /**
     * Function buildOperatorLessThanOrEqualTo
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 30:01
     */
    public function buildOperatorLessThanOrEqualTo(Builder $db, $id, $field = 'id')
    {
        $db->where($this->table . '.' . $field, self::OPERATOR_LESS_THAN_OR_EQUAL_TO, $id);

        return $db;
    }

    /**
     * Function buildOperatorGreaterThanOrEqualTo
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 29:57
     */
    public function buildOperatorGreaterThanOrEqualTo(Builder $db, $id, $field = 'id')
    {
        $db->where($this->table . '.' . $field, self::OPERATOR_GREATER_THAN_OR_EQUAL_TO, $id);

        return $db;
    }

    /**
     * Function buildOperatorSpaceShipTo
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $id
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 29:52
     */
    public function buildOperatorSpaceShipTo(Builder $db, $id, $field = 'id')
    {
        $db->where($this->table . '.' . $field, self::OPERATOR_IS_SPACESHIP, $id);

        return $db;
    }

    /**
     * Function bindOrderBy
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $orderByField
     * @param                                    $defaultField
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 28:46
     */
    public function bindOrderBy(Builder $db, $orderByField, $defaultField = 'updated_at')
    {
        if (isset($orderByField) && is_array($orderByField) && count($orderByField) > 0) {
            foreach ($orderByField as $field) {
                $db->orderBy($this->table . '.' . $field['field_name'], $field['order_value']);
            }
        } elseif (strtolower($defaultField) === 'random') {
            $db->inRandomOrder();
        } else {
            $db->orderByDesc($this->table . '.' . $defaultField);
        }

        return $db;
    }

    /**
     * Function bindOrderByNoDefault
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $orderByField
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 28:41
     */
    public function bindOrderByNoDefault(Builder $db, $orderByField)
    {
        if (isset($orderByField) && is_array($orderByField) && count($orderByField) > 0) {
            foreach ($orderByField as $field) {
                $db->orderBy($this->table . '.' . $field['field_name'], $field['order_value']);
            }
        }

        return $db;
    }

    /**
     * Function filterRecordIsActive
     *
     * @param \Illuminate\Database\Query\Builder $db
     * @param                                    $field
     *
     * @return \Illuminate\Database\Query\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 33:52
     */
    public function filterRecordIsActive(Builder $db, $field = 'status')
    {
        $db->where($this->table . '.' . $field, self::OPERATOR_EQUAL_TO, self::TABLE_OPERATOR_IS_ACTIVE);

        return $db;
    }
}
