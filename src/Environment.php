<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2019-07-06
 * Time: 10:21
 */

namespace nguyenanhung\MyDatabase;

/**
 * Interface Environment
 *
 * @package   nguyenanhung\MyDatabase
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface Environment
{
    const VERSION       = '3.0.0';
    const LAST_MODIFIED = '2021-09-07';
    const AUTHOR_NAME   = 'Hung Nguyen';
    const AUTHOR_EMAIL  = 'dev@nguyenanhung.com';
    const PROJECT_NAME  = 'My Database';
    const TIMEZONE      = 'Asia/Ho_Chi_Minh';

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
    const OPERATOR_IS_SPACESHIP             = '<=>';
    const OPERATOR_IS_IN                    = 'IN';
    const OPERATOR_IS_LIKE                  = 'LIKE';
    const OPERATOR_IS_LIKE_BINARY           = 'LIKE BINARY';
    const OPERATOR_IS_ILIKE                 = 'ilike';
    const OPERATOR_IS_NOT_LIKE              = 'NOT LIKE';
    const OPERATOR_IS_NULL                  = 'IS NULL';
    const OPERATOR_IS_NOT_NULL              = 'IS NOT NULL';
    const ORDER_ASCENDING                   = 'ASC';
    const ORDER_DESCENDING                  = 'DESC';

    /**
     * Hàm lấy thông tin phiên bản Package
     *
     * @return string Current Project Version, VD: 0.1.0
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     */
    public function getVersion();
}
