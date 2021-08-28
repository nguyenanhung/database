<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-02
 * Time: 20:35
 */

namespace nguyenanhung\MyDatabase\Model;

use nguyenanhung\MySQLi\MySQLiBaseModel as BaseMySQLiBaseModel;

/**
 * Class MySQLiBaseModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class MySQLiBaseModel extends BaseMySQLiBaseModel
{
    /**
     * MySQLiBaseModel constructor.
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct()
    {
        parent::__construct();
    }
}
