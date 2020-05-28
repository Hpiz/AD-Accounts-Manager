<?php


namespace System;


use Monolog\Handler\StreamHandler;
use VisualAppeal\AutoUpdate;

class Updater
{
    /**
     * @var AutoUpdate
     */
    private $updater;

    private $lastCheckedFile = WRITEPATH . DIRECTORY_SEPARATOR . "lastUpdateCheck.log";
    /**
     * @var null|int
     */
    private int $lastChecked = 0;
//    private int $updateCheckInterval = (60 * 60 * 12);
    private int $updateCheckInterval = (12);
    private $logger;
    private $timeout = 120;
    private $checkSSL = true;
    private $url;
    private $currentVersion;
    private $tempFilePath;
    private $destFilePath;
    private $logFile = ROOTPATH . DIRECTORY_SEPARATOR . "writable" . DIRECTORY_SEPARATOR . "update.log";

    /**
     * Update constructor.
     *
     * @param AutoUpdate $updater
     */
    public function __construct(string $url, string $tempFilePath, string $destFilePath, int $currentVersion)
    {
        $this->logger = SystemLogger::get();
        $this->logger->info('Updater loaded');
        $this->logger->info(time());
        $this->lastChecked = (int)File::getContents($this->lastCheckedFile);
        $this->tempFilePath = $tempFilePath;
        $this->destFilePath = $destFilePath;
        $this->url = $url;
        $this->currentVersion = $currentVersion;
    }

    public
    function connectToUpdateServer()
    {
        $this->updater = new AutoUpdate($this->tempFilePath, $this->destFilePath, $this->timeout);
        $this->updater->setCurrentVersion(Core::getVersion())
            ->setUpdateUrl($this->url)
            ->addLogHandler(new StreamHandler($this->logFile))
            ->setSslVerifyHost($this->checkSSL);


    }

    public function isUpdateAvailable()
    {
        $this->connectToUpdateServer();
        if (time() - $this->lastChecked > $this->updateCheckInterval) {
            $this->logger->info("Checking for a new version");
            try {


                if ($this->updater->checkUpdate()) {
                    return true;
                } else {
                    // No new update
                    $this->logger->info('Your application is up to date');
                    return false;
                }
            } catch
            (\Exception $ex) {
                $this->logger->error($ex);

            }

        }

    }

    public
    function getLatestVersion()
    {
        return $this->updater->getLatestVersion();
    }

    /**
     * @return string
     */
    public
    function getLastCheckedFile(): string
    {
        return $this->lastCheckedFile;
    }

    /**
     * @param string $lastCheckedFile
     *
     * @return Updater
     */
    public function setLastCheckedFile(string $lastCheckedFile): Updater
    {
        $this->lastCheckedFile = $lastCheckedFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Updater
     */
    public function setUrl(string $url): Updater
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentVersion(): int
    {
        return $this->currentVersion;
    }

    /**
     * @param int $currentVersion
     *
     * @return Updater
     */
    public function setCurrentVersion(int $currentVersion): Updater
    {
        $this->currentVersion = $currentVersion;
        return $this;
    }

    /**
     * @return string
     */
    public function getTempFilePath(): string
    {
        return $this->tempFilePath;
    }

    /**
     * @param string $tempFilePath
     *
     * @return Updater
     */
    public function setTempFilePath(string $tempFilePath): Updater
    {
        $this->tempFilePath = $tempFilePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestFilePath(): string
    {
        return $this->destFilePath;
    }

    /**
     * @param string $destFilePath
     *
     * @return Updater
     */
    public function setDestFilePath(string $destFilePath): Updater
    {
        $this->destFilePath = $destFilePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }

    /**
     * @param string $logFile
     *
     * @return Updater
     */
    public function setLogFile(string $logFile): Updater
    {
        $this->logFile = $logFile;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLastChecked(): ?int
    {
        return $this->lastChecked;
    }

    /**
     * @param int|null $lastChecked
     *
     * @return Updater
     */
    public function setLastChecked(?int $lastChecked): Updater
    {
        $this->lastChecked = $lastChecked;
        return $this;
    }

    /**
     * @return int
     */
    public function getUpdateCheckInterval(): int
    {
        return $this->updateCheckInterval;
    }

    /**
     * @param int $updateCheckInterval
     *
     * @return Updater
     */
    public function setUpdateCheckInterval(int $updateCheckInterval): Updater
    {
        $this->updateCheckInterval = $updateCheckInterval;
        return $this;
    }

    /**
     * @return DatabaseLogger|SystemLogger|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param DatabaseLogger|SystemLogger|null $logger
     *
     * @return Updater
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     *
     * @return Updater
     */
    public
    function setTimeout(int $timeout): Updater
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCheckSSL(): bool
    {
        return $this->checkSSL;
    }

    /**
     * @param bool $checkSSL
     *
     * @return Updater
     */
    public function setCheckSSL(bool $checkSSL): Updater
    {
        $this->checkSSL = $checkSSL;
        return $this;
    }


}