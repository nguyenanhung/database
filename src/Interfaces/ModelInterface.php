<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/16/18
 * Time: 16:35
 */

namespace nguyenanhung\MyDatabase\Interfaces;

/**
 * Interface ModelInterface
 *
 * @package   nguyenanhung\MyDatabase\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface ModelInterface
{
    const OPERATOR_EQUAL_TO                 = '=';
    const OP_EQ                             = '=';
    const OPERATOR_NOT_EQUAL_TO             = '!=';
    const OP_NE                             = '!=';
    const OPERATOR_LESS_THAN                = '<';
    const OP_LT                             = '<';
    const OPERATOR_LESS_THAN_OR_EQUAL_TO    = '<=';
    const OP_LTE                            = '<=';
    const OPERATOR_GREATER_THAN             = '>';
    const OP_GT                             = '>';
    const OPERATOR_GREATER_THAN_OR_EQUAL_TO = '>=';
    const OP_GTE                            = '>=';
    const OPERATOR_IS_LIKE                  = 'like';
    const OPERATOR_IS_NULL                  = 'is null';
    const OPERATOR_IS_NOT_NULL              = 'is not null';
    const ORDER_ASCENDING                   = 'asc';
    const ORDER_DESCENDING                  = 'desc';
}
