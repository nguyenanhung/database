<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-01
 * Time: 20:42
 */

namespace nguyenanhung\MyDatabase\Model;

use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;
use nguyenanhung\MyDatabase\Interfaces\ModelInterface;

/**
 * Class ConnectionBaseModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class ConnectionBaseModel implements ProjectInterface, ModelInterface, ConnectionBaseModelInterface
{
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Debug */
    protected $debug;
    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database;
    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table;
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Capsule Manager \Illuminate\Database\Capsule\Manager */
    protected $db;
    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = FALSE;
    /**
     * @var null|string Cấu hình Level Debug
     * @see https://github.com/nguyenanhung/my-debug/blob/master/src/Interfaces/DebugInterface.php
     */
    public $debugLevel = NULL;
    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = NULL;
    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = NULL;
    /** @var string Primary Key Default */
    public $primaryKey = 'id';

    /**
     * ConnectionBaseModel constructor.
     */
    public function __construct()
    {
        $this->debug = new Debug();
        if ($this->debugStatus === TRUE) {
            $this->debug->setDebugStatus($this->debugStatus);
            if ($this->debugLevel) {
                $this->debug->setGlobalLoggerLevel($this->debugLevel);
            }
            if ($this->debugLoggerPath) {
                $this->debug->setLoggerPath($this->debugLoggerPath);
            }
            if (empty($this->debugLoggerFilename)) {
                $this->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
            }
            $this->debug->setLoggerSubPath(__CLASS__);
            $this->debug->setLoggerFilename($this->debugLoggerFilename);
        }
    }

    /**
     * ConnectionBaseModel destructor.
     */
    public function __destruct()
    {
    }

    /**
     * Function getVersion
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 20:43
     *
     * @return mixed
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}
