<?php

namespace common\models\xml;

use DOMDocument;
use DOMElement;

final class XmlCreator
{
    private string $path;
    private DOMDocument $dom;
    private array $data = [];

    public function __construct(string $path = 'frontend/web/file.xml')
    {
        $this->path = $path;

        $this->dom = new DOMDocument("1.0", "utf-8");
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function create():  DOMDocument
    {
        $this->run();
        return $this->dom;
    }

    public function save():  bool
    {
        $this->run();

        return (bool)$this->dom->save($this->path);
    }

    private function run()
    {
        if (empty($this->data)) {
            return null;
        }

        $this->createTree($this->data, $this->dom);
    }

    private function createTree(array $list, DOMElement|DOMDocument &$parent): void
    {
        foreach ($list as $item) {
            $element = $this->createElement($item);

            foreach ($item['attributes'] ?? [] as $attributeName => $attributeValue) {
                $element->setAttribute($attributeName, $attributeValue);
            }

            if (!empty($item['elements'])) {
                $this->createTree($item['elements'], $element);
            }

            $parent->appendChild($element);
        }
    }

    private function createElement(array $info): DOMElement
    {
        try {
            return $this->dom->createElement($info['title'], $info['value'] ?? '');
        } catch (\DOMException $e) {
            var_dump($e->getMessage());exit();
        }
    }
}