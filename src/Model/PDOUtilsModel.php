<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-01
 * Time: 21:50
 */

namespace nguyenanhung\MyDatabase\Model;

use nguyenanhung\PDO\MySQLPDOUtilsModel as BaseMySQLPDOUtilsModel;

/**
 * Class PDOUtilsModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class PDOUtilsModel extends BaseMySQLPDOUtilsModel
{
    /**
     * PDOUtilsModel constructor.
     *
     * @param array $database
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct(array $database = [])
    {
        parent::__construct($database);
    }
}
