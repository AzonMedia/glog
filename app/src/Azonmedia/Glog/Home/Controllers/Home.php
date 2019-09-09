<?php

namespace Azonmedia\Glog\Home\Controllers;

use Guzaba2\Mvc\Controller;
use Guzaba2\Http\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Guzaba2\Translator\Translator as t;

class Home extends Controller
{
    public function view() : ResponseInterface
    {

        $Response = parent::get_structured_ok_response();
        $struct =& $Response->getBody()->getStructure();
        $struct['message'] = sprintf(t::_('This is the home page baby'));
        
        $log_entry = new \Azonmedia\Glog\LogEntries\Models\LogEntry(0);
        $log_entry->log_entry_content = 'content';
        $log_entry->save();
        
        return $Response;
    }

    /* Test for uploading files
    public function create() : ResponseInterface
    {

        $Response = parent::get_structured_ok_response();
        $struct =& $Response->getBody()->getStructure();
        $struct['message'] = sprintf(t::_('This is the Create page'));

        $Request = parent::get_request();
		$uploadedFiles = uploadedFile::parseUploadedFiles($Request->getUploadedFiles());

		$struct['uploaded_files_messages'] = [];

        if (!empty($uploadedFiles))  {

            foreach ($uploadedFiles as $uploadedFile) {

                if (is_array($uploadedFile)) {
                    foreach ($uploadedFile as $subUploadedFile) {
                        $uploaded_file_result = $this->uploadFile($subUploadedFile, $message);
                        $struct['uploaded_files_messages'][] = $message;
                    }
                } else {
                    $uploaded_file_result = $this->uploadFile($uploadedFile, $message);
                    $struct['uploaded_files_messages'][] = $message;
                }
            }
        }

        return $Response;
    }

    private function uploadFile(\Guzaba2\Http\UploadedFile $fileToUpload, &$message) : String
    {
        $directory = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'uploads';

        $extension = pathinfo($fileToUpload->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        try {
            $fileToUpload->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
            $message = '<h3>File ' . $fileToUpload->getClientFilename() . ' is uploaded in ' . $directory . DIRECTORY_SEPARATOR . $filename . '!</h3>';
            return true;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            //$message = $directory . DIRECTORY_SEPARATOR . $filename;
            return false;
        }
    }
    */
}