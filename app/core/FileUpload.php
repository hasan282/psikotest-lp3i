<?php

class FileUpload
{
    private $FileExtension = array();
    private $FileLocation;
    private $FileSizeLimit;
    private $ErrorMessage;
    private $UploadResult;

    public function __construct($Location = IMG_LOCATION, $Extension = FILE_EXTENSION)
    {
        $this->FileLocation = $Location . '/';
        $this->FileExtension = explode('|', $Extension);
        $this->FileSizeLimit = LIMIT_FILESIZE;
    }

    public function UploadFile($File)
    {
        $FileSize = $this->_FileSize($File);
        $FileExtension = $this->_CheckExtension($File);
        if ($FileSize == 0) {
            $this->UploadResult = array('status' => 1, 'file' => null);
        } else {
            $NewName = 'IMG_' . date('YmdHis') . '.' . $FileExtension['ext'];
            $FileTarget = $this->FileLocation . $NewName;
            $ErrorCheck = $this->_UploadErrorCheck($FileTarget, $FileSize, $FileExtension['status']);
            if ($ErrorCheck) {
                $this->UploadResult = array('status' => 0, 'error' => $this->ErrorMessage);
            } else {
                if (move_uploaded_file($File['tmp_name'], $FileTarget)) {
                    $this->UploadResult = array('status' => 1, 'file' => $NewName);
                } else {
                    $this->UploadResult = array('status' => 0, 'error' => 'failed');
                }
            }
        }
        return $this->UploadResult;
    }

    private function _UploadErrorCheck($FileLoc, $FileSize, $FileExt)
    {
        $UploadError = false;
        if (file_exists($FileLoc)) {
            $this->ErrorMessage = 'exist';
            $UploadError = true;
        }
        if ($FileSize > $this->FileSizeLimit) {
            $this->ErrorMessage = 'over';
            $UploadError = true;
        }
        if (!$FileExt) {
            $this->ErrorMessage = 'unmatch';
            $UploadError = true;
        }
        return $UploadError;
    }

    public function DeleteFile($File)
    {
        unlink($this->FileLocation . $File);
    }

    public function ReplaceFile($OldFile, $NewFile)
    {
        $UpResult = $this->UploadFile($NewFile);
        if ($UpResult['status'] > 0) {
            if ($UpResult['file'] != null) {
                $ReplaceResult = array('status' => 1, 'file' => $UpResult['file']);
                $this->DeleteFile($OldFile);
            } else {
                $ReplaceResult = array('status' => 1, 'file' => null);
            }
        } else {
            $ReplaceResult = array('status' => 0, 'error' => $UpResult['error']);
        }
        return $ReplaceResult;
    }

    private function _FileSize($File)
    {
        return $File['size'];
    }

    private function _CheckExtension($File)
    {
        $ExplodeFileName = explode('.', $File['name']);
        $Extension = strtolower(end($ExplodeFileName));
        $TrueExtension = false;
        foreach ($this->FileExtension as $Ext) {
            if ($Ext == $Extension) {
                $TrueExtension = true;
            }
        }
        $ReturnExtension = array('status' => $TrueExtension, 'ext' => $Extension);
        return $ReturnExtension;
    }
}
