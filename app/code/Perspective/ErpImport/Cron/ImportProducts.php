<?php
declare(strict_types=1);

namespace Perspective\ErpImport\Cron;

use Magento\Framework\Exception\LocalizedException;
use Perspective\ErpImport\Logger\ProductImportLogger;
use Perspective\ErpImport\Service\BulkManager;
use Perspective\ErpImport\Service\BulkValidator;

/**
 * ImportProducts Cron
 */
class ImportProducts
{
    /**
     * @var ProductImportLogger
     */
    protected ProductImportLogger $logger;

    /**
     * @var BulkManager
     */
    protected BulkManager $bulkManager;

    /**
     * @var BulkValidator
     */
    protected BulkValidator $bulkValidator;

    /**
     * ImportProducts constructor.
     *
     * @param ProductImportLogger $logger
     * @param BulkManager $bulkManager
     * @param BulkValidator $bulkValidator
     */
    public function __construct(
        ProductImportLogger $logger,
        BulkManager $bulkManager,
        BulkValidator $bulkValidator,
    ) {
        $this->logger = $logger;
        $this->bulkManager = $bulkManager;
        $this->bulkValidator = $bulkValidator;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute(): void
    {
        $archiveDir = BP . '/var/import/archive/';
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0777, true);
        }
        $logDir = BP . '/var/log/erp_import/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        //start timer
        $startTime = microtime(true);
        $this->logger->info("START IMPORT");

        $importFile = BP . '/var/import/products.csv';
        $batchSize = 100;
        $batch = [];
        $rowNumber = 0;
        $updatedSuccess = 0;
        $updatedFail = 0;

        if (!file_exists($importFile)) {
            $this->logger->info("CSV file not found: {$importFile}");
        } else {
            if (($handle = fopen($importFile, 'r')) !== false) {
                $headers = fgetcsv($handle);
                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    $row = array_combine($headers, $data);

                    $rowIsValid = $this->bulkValidator->rowIsValid($row);
                    if (!$rowIsValid['valid']) {
                        $this->logger->info("ERROR. [{$rowNumber}]Sku: {$row['sku']}. Reason: {$rowIsValid['message']}");
                        $updatedFail++;
                        continue;
                    }

                    $batch[] = $row;

                    if (count($batch) === $batchSize) {
                        $bulkResponse = $this->bulkManager->process($batch);
                        if ($bulkResponse['status'] != 202) {
                            $this->logger->info("Bulk failed, error: " . $bulkResponse['body']['message'] . ' Status: ' . $bulkResponse['status']);
                            $updatedFail += count($batch);
                        } else {
                            $this->logger->info("Bulk scheduled, UUID: " . $bulkResponse['body']['bulk_uuid'] . ' Status: ' . $bulkResponse['status']);
                            $updatedSuccess += count($batch);
                        }
                        $batch = [];
                    }
                }

                if (!empty($batch)) {
                    $bulkResponse = $this->bulkManager->process($batch);
                    if ($bulkResponse['status'] != 202) {
                        $this->logger->info("Bulk failed, error: " . $bulkResponse['body']['message'] . ' Status: ' . $bulkResponse['status']);
                        $updatedFail += count($batch);
                    } else {
                        $this->logger->info("Bulk scheduled, UUID: " . $bulkResponse['body']['bulk_uuid'] . ' Status: ' . $bulkResponse['status']);
                        $updatedSuccess += count($batch);
                    }
                    $batch = [];
                }

                fclose($handle);
                $archiveFile = $archiveDir . 'products_' . date('Y-m-d') . '.csv';
                rename($importFile, $archiveFile);
                $this->logger->info("CSV file archived to {$archiveFile}");
            }
        }

        $this->logger->info("IMPORT FINISHED. Successfully updated: {$updatedSuccess}. Failed: {$updatedFail}.");
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->logger->info("Execution time: {$executionTime} s");
    }
}
