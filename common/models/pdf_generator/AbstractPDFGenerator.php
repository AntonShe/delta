<?php

namespace common\models\pdf_generator;

use TCPDF;

abstract class AbstractPDFGenerator
{
    protected TCPDF $generator;

    protected array $params = [];
    protected string $filename;
    protected string $rowSpace = '<p style="width: 100%;">&nbsp;</p>';

    abstract protected function buildFile(): void;
    abstract public function saveFile(string $fileName): bool;

    public function __construct(string $filename)
    {
        $this->generator = new TCPDF();
        $this->filename = $filename;
    }

    public function generate(array $params = []): void
    {
        $this->params = $params;

        $this->buildFile();
    }

    public function getFileAsString(): string
    {
        return '';
    }

    public function getFileToDownload()
    {
        return;
    }

    public function getFileToView()
    {
        return $this->generator->Output( $this->filename, 'I');
    }

}