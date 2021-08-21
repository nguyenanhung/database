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
 * Interface ProjectInterface
 *
 * @package   nguyenanhung\MyDatabase
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface ProjectInterface
{
    const VERSION       = '2.0.5.1';
    const LAST_MODIFIED = '2021-08-21';
    const AUTHOR_NAME   = 'Hung Nguyen';
    const AUTHOR_EMAIL  = 'dev@nguyenanhung.com';
    const PROJECT_NAME  = 'My Database';
    const TIMEZONE      = 'Asia/Ho_Chi_Minh';

    /**
     * Hàm lấy thông tin phiên bản Package
     *
     * @return mixed|string Current Project Version, VD: 0.1.0
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     */
    public function getVersion();
}
