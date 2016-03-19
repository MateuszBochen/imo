<?php

class FilesChecker
{
    const UPLOAD_DIR = '/uploads';
    const FILE_WITH_UPDATES = '/uploads/times.txt';

    private $hiddenFiles = ['.', '..', 'times.txt', 'backup', 'logs', 'public_html', 'svn_repo', 'imo'];
    private $lastBigUpdate = 0;
    private $lastSmallUpdate = 0;

    private $newSmallFiles = [];
    private $newBigFiles = [];

    public function __construct()
    {
        $this->sacn();
    }

    public function __destruct()
    {
        file_put_contents(ROOT_DIR.self::FILE_WITH_UPDATES, $this->lastBigUpdate.';'.$this->lastSmallUpdate);
    }

    public function getNewSallFile()
    {
        $new = array_shift($this->newSmallFiles);

        if ($new) {
            $this->lastSmallUpdate = $this->getTimestamp($new);
            return ROOT_DIR.self::UPLOAD_DIR.'/'.$new;
        }

        return false;
    }

    public function getNewBigFile()
    {
        $new = array_shift($this->newBigFiles);

        if ($new) {
            $this->lastBigUpdate = $this->getTimestamp($new);
            return ROOT_DIR.self::UPLOAD_DIR.'/'.$new;
        }

        return false;
    }

    private function getLastUpdateDate()
    {
        if (!file_exists(ROOT_DIR.self::FILE_WITH_UPDATES)) {
            return false;
        }

        list($this->lastBigUpdate, $this->lastSmallUpdate) = explode(';', file_get_contents(ROOT_DIR.self::FILE_WITH_UPDATES));
    }

    private function sacn()
    {
        $files = scandir (ROOT_DIR.self::UPLOAD_DIR);
        $files = array_diff($files, $this->hiddenFiles);
        
        $this->sortFilesByDate($files);
    }

    private function sortFilesByDate($files)
    {
        foreach ($files as $file) {
            
            $date = $this->getTimestamp($file);

            if ($date > $this->lastSmallUpdate) {
                $this->newSmallFiles[$date] = $file;
            }

            if ($date > $this->lastBigUpdate) {
                $this->newBigFiles[$date] = $file;
            }
        }

        ksort($this->newSmallFiles);
        ksort($this->newBigFiles);
    }

    private function getTimestamp($fileName)
    {

        if (!is_file( ROOT_DIR.self::UPLOAD_DIR.'/'.$fileName)) {
            return false;
        }

        $a = explode('_', $fileName);

        return (int) $a[1];
    }
}
