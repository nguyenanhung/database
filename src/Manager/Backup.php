<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/17/18
 * Time: 01:51
 */

namespace nguyenanhung\MyDatabase\Manager;

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

use BackupManager\Config\Config;
use BackupManager\Filesystems;
use BackupManager\Databases;
use BackupManager\Compressors;
use BackupManager\Manager;
use BackupManager\Filesystems\Destination;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;
use nguyenanhung\MyDatabase\Interfaces\BackupInterface;

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

    /**
     * Backup constructor.
     */
    public function __construct()
    {
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
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/storage.php
     */
    public function setStorage($storage = [])
    {
        $this->storage = $storage;
    }

    /**
     * Hàm set File dữ liệu Storage
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param string $storageFile File dữ liệu storage
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/storage.php
     */
    public function setStorageFile($storageFile = '')
    {
        $this->storageFile = $storageFile;
    }

    /**
     * Hàm set mảng dữ liệu Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param array $database Mảng dữ liệu database
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/database.php
     */
    public function setDatabase($database = [])
    {
        $this->database = $database;
    }

    /**
     * Hàm set File dữ liệu Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param string $databaseFile File dữ liệu database
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/database.php
     */
    public function setDatabaseFile($databaseFile = '')
    {
        $this->databaseFile = $databaseFile;
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
     * Function backup
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:09
     *
     * @throws \BackupManager\Compressors\CompressorTypeNotSupported
     * @throws \BackupManager\Config\ConfigFieldNotFound
     * @throws \BackupManager\Config\ConfigFileNotFound
     * @throws \BackupManager\Config\ConfigNotFoundForConnection
     * @throws \BackupManager\Databases\DatabaseTypeNotSupported
     * @throws \BackupManager\Filesystems\FilesystemTypeNotSupported
     */
    public function backup()
    {
        $manager = $this->boot();
        $manager
            ->makeBackup()
            ->run('development', [
                new Destination('local', 'test/backup.sql'),
                new Destination('s3', 'test/dump.sql')
            ], 'gzip');
    }

    /**
     * Function restore
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:10
     *
     * @throws \BackupManager\Compressors\CompressorTypeNotSupported
     * @throws \BackupManager\Config\ConfigFieldNotFound
     * @throws \BackupManager\Config\ConfigFileNotFound
     * @throws \BackupManager\Config\ConfigNotFoundForConnection
     * @throws \BackupManager\Databases\DatabaseTypeNotSupported
     * @throws \BackupManager\Filesystems\FilesystemTypeNotSupported
     */
    public function restore()
    {
        $manager = $this->boot();
        $manager
            ->makeRestore()
            ->run(
                's3',
                'test/backup.sql.gz',
                'production',
                'gzip'
            );
    }
}
