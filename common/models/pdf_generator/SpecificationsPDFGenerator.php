<?php

namespace common\models\pdf_generator;

use yii\base\UnknownMethodException;

class SpecificationsPDFGenerator extends AbstractPDFGenerator
{
    protected function buildFile(): void
    {
        $productRows = '';
        $idOrder = $this->params['status']['id'];
        $itemsCount = count($this->params['products']);
        $dateCreate = date('d.m.Y', strtotime($this->params['status']['date_create']));

        $this->generator->SetFont('dejavusans', '', 10, '', true);
        $this->generator->AddPage();

        foreach ($this->params['products'] as $index => $product) {
            $num = $index + 1;
            $productRows .= '<tr>
		            <td style="width: 30px; text-align: center; vertical-align: middle;">'.$num.'</td>
		            <td style=" text-align: center;">'.$product['title'].'</td>
		            <td style="text-align: center;">'.$product['quantityCart'].'</td>
		            <td style="text-align: center;">'.$product['nds'].'</td>
		            <td style="text-align: center;">'.$product['priceInOrder'].',00</td>
		            <td style="text-align: center;">'.$product['quantityCart'] * $product['priceInOrder'].',00</td>
	            </tr>';
        }

        $totalSum = floatval($this->params['status']['orderPrice']);
        $ceilVal = explode('.', $totalSum);
        $ceilVal[1] = empty($ceilVal[1]) ? '00' : $ceilVal[1];

        $html = <<<EOD
            <p style="width: 100%; text-align: right;">
                <span>Приложение № 1 к Заказу № $idOrder</span><br/>
                <span>от $dateCreate</span>
            </p>
            $this->rowSpace 
            $this->rowSpace           
            <h1 style="width: 100%; text-align: center;">СПЕЦИФИКАЦИЯ</h1>
            <table border="1" style="width: 100%;">
	            <tr>
		            <td style="width: 30px; text-align: center; vertical-align: middle;">№</td>
		            <td style="width: 330px; text-align: center;">Наименование товара</td>
		            <td style="width: 50px; text-align: center;">Кол-во</td>
		            <td style="width: 30px; text-align: center;">НДС</td>
		            <td style="width: 50px; text-align: center;">Цена</td>
		            <td style="width: 50px; text-align: center;">Сумма</td>
	            </tr>
	            $productRows
	            <tr>
	                <td border="0" colspan="5">Всего к оплате</td>
		            <td >$totalSum</td>
                </tr>
            </table>
            $this->rowSpace
            $this->rowSpace
            <p>Всего $itemsCount наименований на сумму  $ceilVal[0] рублей $ceilVal[1] копеек</p>
            $this->rowSpace
            <p>
                <span>Продавец ООО "Цунами Букс"</span><br/>
                <span>&nbsp;&nbsp;Менеджер (по доверенности)  _______________ <span style="text-decoration: underline;">(Залипаев А.Г.)</span></span>
            </p>
EOD;

        $this->generator->writeHTMLCell(0, 0, '', '', $html, 0, 0, 0, true, '', true);
    }

    public function saveFile(string $fileName): bool
    {
        throw new UnknownMethodException('Method not realized!');
    }
}
