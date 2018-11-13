<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/17/18
 * Time: 01:51
 */

namespace nguyenanhung\MyDatabase\Manager;

use BackupManager\Config\Config;
use BackupManager\Filesystems;
use BackupManager\Databases;
use BackupManager\Compressors;
use BackupManager\Manager;
use BackupManager\Filesystems\Destination;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;
use nguyenanhung\MyDatabase\Interfaces\BackupInterface;
use nguyenanhung\MyDatabase\Repository\DataRepository;

/**
 * Class Backup
 *
 * Class Backup Database, sử dụng Backup Manager
 *
 * @see       https://packagist.org/packages/backup-manager/backup-manager
 *
 * @package   nguyenanhung\MyDatabase\Manager
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 * @since     2018-10-17
 * @version   0.1.2
 */
class Backup implements ProjectInterface, BackupInterface
{
    /** @var null|array Mảng dữ liệu cấu hình Storage */
    protected $storage = NULL;
    /** @var null|string File dữ liệu cấu hình Storage */
    protected $storageFile = NULL;
    /** @var null|array Mảng dữ liệu cấu hình Database */
    protected $database = NULL;
    /** @var null|string File dữ liệu cấu hình Database */
    protected $databaseFile = NULL;
    /** @var null|string Folder lưu trữ file Backup, VD: /your/to/path */
    protected $folderBackup = NULL;
    /** @var object Đối tượng khởi tạo đến class \nguyenanhung\MyDebug\Debug */
    private $debug;

    /**
     * Backup constructor.
     */
    public function __construct()
    {
        $this->debug = new Debug();
    }

    /**
     * Backup destructor.
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /**
     * Hàm lấy thông tin phiên bản Package
     *
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     *
     * @return mixed|string Current Project Version, VD: 0.1.0
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Hàm set mảng dữ liệu Storage
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param array $storage Mảng dữ liệu storage
     *
     * @return  $this;
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/storage.php
     */
    public function setStorage($storage = [])
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Hàm set File dữ liệu Storage
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param string $storageFile File dữ liệu storage
     *
     * @return  $this;
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/storage.php
     */
    public function setStorageFile($storageFile = '')
    {
        $this->storageFile = $storageFile;
        $fileContent       = DataRepository::getDataContent($this->storageFile);
        d($fileContent);

        return $this;
    }

    /**
     * Hàm set mảng dữ liệu Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param array $database Mảng dữ liệu database
     *
     * @return  $this;
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/database.php
     */
    public function setDatabase($database = [])
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Hàm set File dữ liệu Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param string $databaseFile File dữ liệu database
     *
     * @return  $this;
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/database.php
     */
    public function setDatabaseFile($databaseFile = '')
    {
        $this->databaseFile = $databaseFile;
        $fileContent        = DataRepository::getDataContent($this->databaseFile);
        d($fileContent);

        return $this;
    }

    /**
     * Hàm set Folder lưu trữ file Backup
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 09:07
     *
     * @param string $folderBackup Folder lưu trữ file Backup, VD: /your/to/path
     */
    public function setFolderBackup($folderBackup = '')
    {
        $this->folderBackup = $folderBackup;
    }

    /**
     * Hàm bootstrap cho tiến trình Backup và Restore Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:07
     *
     * @return \BackupManager\Manager
     * @throws \BackupManager\Config\ConfigFileNotFound
     */
    public function boot()
    {
        // build providers
        $filesystems = new Filesystems\FilesystemProvider(Config::fromPhpFile($this->storageFile));
        $filesystems->add(new Filesystems\Awss3Filesystem);
        $filesystems->add(new Filesystems\GcsFilesystem);
        $filesystems->add(new Filesystems\DropboxFilesystem);
        $filesystems->add(new Filesystems\FtpFilesystem);
        $filesystems->add(new Filesystems\LocalFilesystem);
        $filesystems->add(new Filesystems\RackspaceFilesystem);
        $filesystems->add(new Filesystems\SftpFilesystem);
        $databases = new Databases\DatabaseProvider(Config::fromPhpFile($this->databaseFile));
        $databases->add(new Databases\MysqlDatabase);
        $databases->add(new Databases\PostgresqlDatabase);
        $compressors = new Compressors\CompressorProvider;
        $compressors->add(new Compressors\GzipCompressor);
        $compressors->add(new Compressors\NullCompressor);

        // build manager
        return new Manager($filesystems, $databases, $compressors);
    }

    /**
     * Hàm backup Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 09:28
     *
     * @param string $database Tên cấu hình Database
     *
     * @return bool|string TRUE nếu thành công, Message Error nếu có lỗi xảy ra
     */
    public function backup($database = '')
    {
        d($database);
        d($this->storageFile);
        d($this->databaseFile);
        d($this->folderBackup);
        try {
            $manager = $this->boot();
            $manager
                ->makeBackup()
                ->run($database, [
                    new Destination('local', $this->folderBackup . '/backup-' . $database . '-' . date('Y-m-d') . '.sql')
                ], 'gzip');

            return TRUE;
        }
        catch (\Exception $e) {
            $message = 'Error File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Code: ' . $e->getCode() . ' - Message: ' . $e->getMessage();

            return $message;
        }

    }

    /**
     * Hàm phục hồi CSDL
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 09:29
     *
     * @param string $database Tên cấu hình Database cần phục hồi
     * @param string $filename Đường dẫn đến file sao lưu
     *
     * @return bool|string TRUE nếu thành công, Message Error nếu có lỗi xảy ra
     */
    public function restore($database = '', $filename = '')
    {
        try {
            $manager = $this->boot();
            $manager
                ->makeRestore()
                ->run(
                    'local',
                    $filename,
                    $database,
                    'gzip'
                );

            return TRUE;
        }
        catch (\Exception $e) {
            $message = 'Error File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Code: ' . $e->getCode() . ' - Message: ' . $e->getMessage();

            return $message;
        }
    }
}
