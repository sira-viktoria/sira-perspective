<?php
declare(strict_types=1);

namespace Perspective\ErpImport\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;
use Magento\Framework\Filesystem\Driver\File;
use Monolog\Formatter\LineFormatter;

/**
 * ProductImportHandler Class.
 */
class ProductImportHandler extends Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;
    /**
     * @param File $filesystem
     */
    public function __construct(File $filesystem)
    {
        $this->fileName = '/var/log/erp_import/product_import_' . date('Y-m-d') . '.log';
        parent::__construct($filesystem);

        $formatter = new LineFormatter("[%datetime%] %message%\n", "Y-m-d H:i:s", true, true);
        $this->setFormatter($formatter);
    }
}
