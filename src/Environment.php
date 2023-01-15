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
    public const PROJECT_NAME = 'My Database by HungNG';
    public const VERSION = '3.0.9';
    public const LAST_MODIFIED = '2023-01-14';
    public const AUTHOR_NAME = 'Hung Nguyen';
    public const AUTHOR_EMAIL = 'dev@nguyenanhung.com';
    public const AUTHOR_URL = 'https://nguyenanhung.com';
    public const GITHUB_URL = 'https://github.com/nguyenanhung/database';
    public const PACKAGES_URL = 'https://packagist.org/packages/nguyenanhung/database';
    public const TIMEZONE = 'Asia/Ho_Chi_Minh';
    public const OPERATOR_EQUAL_TO = '=';
    public const OP_EQ = '=';
    public const OPERATOR_NOT_EQUAL_TO = '!=';
    public const OP_NE = '!=';
    public const OPERATOR_LESS_THAN = '<';
    public const OP_LT = '<';
    public const OPERATOR_LESS_THAN_OR_EQUAL_TO = '<=';
    public const OP_LTE = '<=';
    public const OPERATOR_GREATER_THAN = '>';
    public const OP_GT = '>';
    public const OPERATOR_GREATER_THAN_OR_EQUAL_TO = '>=';
    public const OP_GTE = '>=';
    public const OPERATOR_IS_SPACESHIP = '<=>';
    public const OPERATOR_IS_IN = 'IN';
    public const OPERATOR_IS_LIKE = 'LIKE';
    public const OPERATOR_IS_LIKE_BINARY = 'LIKE BINARY';
    public const OPERATOR_IS_ILIKE = 'ilike';
    public const OPERATOR_IS_NOT_LIKE = 'NOT LIKE';
    public const OPERATOR_IS_NULL = 'IS NULL';
    public const OPERATOR_IS_NOT_NULL = 'IS NOT NULL';
    public const ORDER_ASCENDING = 'ASC';
    public const ORDER_DESCENDING = 'DESC';

    /**
     * Hàm lấy thông tin phiên bản Package
     *
     * @return string Current Project Version, VD: 0.1.0
     *
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     */
    public function getVersion(): string;
}
