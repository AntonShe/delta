<?php

namespace common\models\api;

class FixPatchRequest
{
    public static function parseRequest(){

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'PATCH') { return; }
        if (!isset($_SERVER['CONTENT_TYPE'])){ return; }

        $contentTypeHeader = explode(';', $_SERVER['CONTENT_TYPE']);
        if ($contentTypeHeader[0] !== 'multipart/form-data') { return; }

        $boundary = explode('boundary=', $contentTypeHeader[1])[1];

        $iterator = self::_readInput();

        $bodyParams = [];

        self::_skipBoundary($iterator);
        if (self::_bodyIsEmpty($iterator)) { return; }

        while ($iterator->valid()){

            $header = self::_readHeader($iterator);

            if (is_null($header->filename)) {
                $bodyParams[$header->name] = self::_getParamValue($iterator);
                self::_skipBoundary($iterator);
            }
            else{
                self::_readFile($boundary, $header->filename, $header->fileMIME, $header->name, $iterator);
            }

            if ($iterator->current() === "") { break; }

        }

        \Yii::$app->request->setBodyParams($bodyParams);
        return;

    }

    /**
     * @param \Generator $iterator
     */
    private static function _skipBoundary(\Generator &$iterator) {

        while ($iterator->valid()) {

            if ($iterator->current() === "\n") {
                $iterator->next();
                break;
            }

            $iterator->next();
        }

    }

    /**
     * @param \Generator $iterator
     * @return bool
     */
    private static function _bodyIsEmpty(\Generator &$iterator) {

        return $iterator->current() === "";

    }

    /**
     * @param \Generator $iterator
     * @return __anonymous@2329
     */
    private static function _readHeader(\Generator &$iterator) {

        $header = new class{
            public $name = null;
            public $filename = null;
            public $fileMIME = null;
        };

        while (true) {

            $headerStr = '';
            while ($iterator->current() !== "\n") {
                $headerStr .= $iterator->current();
                $iterator->next();
            }

            if (mb_strpos($headerStr, 'Content-Disposition') !== false) {
                $headerArr = explode(';', $headerStr);

                $header->name = trim(explode('=', $headerArr[1])[1], " \"\r\n");

                if (isset($headerArr[2])){
                    $header->filename = trim(explode('=', $headerArr[2])[1], " \"\r\n");
                }
            }
            elseif (mb_strpos($headerStr, 'Content-Type') !== false) {
                $header->fileMIME = trim(explode(':', $headerStr)[1], " \r\n");
            }

            $iterator->next();

            if ($iterator->current() === "\r") {
                #no more meta data
                $iterator->next(); //skip \r
                $iterator->next(); //skip \n
                return $header;
            }

        }

    }

    private static function _getParamValue(\Generator &$iterator){

        $paramValue = '';

        while ($iterator->current() !== "\r") {
            $paramValue.= $iterator->current();
            $iterator->next();
        }

        $iterator->next(); //skip \r
        $iterator->next(); //skip \n

        if($paramValue === "") {
            #parameter value is empty
            return null;
        }
        else{
            return $paramValue;
        }

    }

    private static function _readFile(string $boundary, $filename, string $fileMIME, string $paramName, \Generator &$iterator) {

        $tmpHandle = fopen(tempnam(sys_get_temp_dir(), 'php'), 'wb');

        while ($iterator->valid()) {

            $b = 0;
            $data = '';

            #collect bytes
            while ($iterator->valid()) {
                $data.= $iterator->current();

                if ($iterator->current() === "\r") { break; }
                if ($b >= 511) { break; }

                $iterator->next();
                $b++;
            }

            #boundary
            if (mb_strpos($data, $boundary) !== false) {
                $iterator->next(); //skip \r
                $iterator->next(); //skip \n
                self::_changeFilesGlobal($tmpHandle, $filename, $fileMIME, $paramName);
                return;
            }
            else{
                #echo 'DATA: '. $data. PHP_EOL;
                fwrite($tmpHandle, $data);
                $iterator->next();
            }

        }

    }

    private static function _changeFilesGlobal($tmpHandle, string $filename, string $fileMIME, string $paramName){

        $tmpFileMeta = stream_get_meta_data($tmpHandle);

        if (preg_match('/(.+)\[(.*)\]/', $paramName, $matches)) {
            if ($matches[2] === "") {
                //Numeric array

                if (!isset($_FILES[$matches[1]])) {
                    $_FILES[$matches[1]] = [];
                }

                $_FILES[$matches[1]]['name'][] = $filename;
                $_FILES[$matches[1]]['type'][] = $fileMIME;
                $_FILES[$matches[1]]['tmp_name'][] = $tmpFileMeta['uri'];
                $_FILES[$matches[1]]['error'][] = 0;
                $_FILES[$matches[1]]['size'][] = filesize($tmpFileMeta['uri']);

            }
            else{
                #associative array
                if (!isset($_FILES[$matches[1]])) {
                    $_FILES[$matches[1]] = [];
                }

                $_FILES[$matches[1]]['name'][$matches[2]] = $filename;
                $_FILES[$matches[1]]['type'][$matches[2]] = $fileMIME;
                $_FILES[$matches[1]]['tmp_name'][$matches[2]] = $tmpFileMeta['uri'];
                $_FILES[$matches[1]]['error'][$matches[2]] = 0;
                $_FILES[$matches[1]]['size'][$matches[2]] = filesize($tmpFileMeta['uri']);
            }

        }
        else{

            $_FILES[$paramName] = [
                'name' => $filename,
                'type' => $fileMIME,
                'tmp_name' => $tmpFileMeta['uri'],
                'error' => 0,
                'size' => filesize($tmpFileMeta['uri'])
            ];

        }

    }

    /**
     * @return \Generator
     */
    private static function _readInput(){

        $handle = fopen('php://input', 'rb');
        $i = 0;
        while (!feof($handle)) {
            yield stream_get_contents($handle, 1, $i++);
        }

        fclose($handle);

    }
}