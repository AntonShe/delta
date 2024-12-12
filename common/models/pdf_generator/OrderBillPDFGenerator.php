<?php

namespace common\models\pdf_generator;

use yii\base\UnknownMethodException;

class OrderBillPDFGenerator extends AbstractPDFGenerator
{
    protected function buildFile(): void
    {
        $productRows = '';
        $ndsRows = '';
        $idOrder = $this->params['status']['id'];
        $legalPayer = $this->params['user']['profile'][0];
        $legalGetter = empty($this->params['user']['profile'][1])
            ? $legalPayer
            : $this->params['user']['profile'][1];
        $itemsCount = count($this->params['products']);
        $deliveryIndex = $itemsCount + 1;
        $totalPrice = $this->params['status']['orderPrice'] + $this->params['delivery']['price'];
        $dateCreate = date('d.m.Y', strtotime($this->params['status']['date_create']));
        $ndsCounter = [
            0 => 0,
            10 => 0,
            20 => $this->params['delivery']['price'],
        ];

        foreach ($this->params['products'] as $index => $product) {
            $num = $index + 1;
            $ndsCounter[$product['nds']] +=$product['quantityCart'] * $product['priceInOrder'];
            $productRows .= '<tr>
		            <td style="width: 30px; text-align: center; vertical-align: middle;">'.$num.'</td>
		            <td style=" text-align: center;">'.$product['title'].'</td>
		            <td style="text-align: center;">'.$product['quantityCart'].'</td>
		            <td style="text-align: center;">'.$product['nds'].'</td>
		            <td style="text-align: center;">'.$product['priceInOrder'].',00</td>
		            <td style="text-align: center;">'.$product['quantityCart'] * $product['priceInOrder'].',00</td>
	            </tr>';
        }

        foreach ($ndsCounter as $grade => $nds) {
            if ($nds == 0) continue;

            $gradeRow = $grade == 0
                ? "Итого сумма без НДС"
                : "Итого сумма по ставке НДС {$grade}%:";
            $ndsRows .= '
                <tr>
                    <td colspan="5" style="text-align: right;">'. $gradeRow .'</td>
                    <td>'.$nds.',00</td>
                </tr>';

            if ( $grade > 0) {
                $ndsSum = number_format(round(($nds * $grade) / ($grade + 100), 2), 2, ',', '');
                $ndsRows .= '
                <tr>
                    <td colspan="5" style="text-align: right;">В том числе НДС '. $grade .' %:</td>
                    <td>'.$ndsSum.'</td>
                </tr>';
            }
        }

        $this->generator->SetFont('dejavusans', '', 10, '', true);
        $this->generator->AddPage();

        $html = <<<EOD
<p>
    <span>ООО "Цунами Букс"</span><br/>
    <span>&nbsp;</span><br/>
    <span>Адрес:	129626, Москва г, 1-я Мытищинская ул, дом 3, строение 1</span>
</p>
<table border="1">
    <tr>
        <td>ИНН 9725093665</td>
        <td>КПП 771701001</td>
        <td rowspan="2">Счет №</td>
        <td rowspan="2">40702810538000160286</td>
    </tr>
    <tr>
        <td colspan="2">Получатель : ООО "Цунами Букс"</td>
    </tr>
    <tr>
		<td colspan="2" rowspan="2">Банк Получателя :ПАО СБЕРБАНК г.Москва</td>
		<td>К/С</td>
		<td>30101810400000000225</td>
	</tr>
	<tr>
		<td>БИК</td>
		<td>044525225</td>
	</tr>
</table>
<p>
    <span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span>
    <span>Счет № $idOrder</span>
    <span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span>
    <span>от $dateCreate</span><br/>
    <span>&nbsp;</span><br/>
    <span>Покупатель:</span><span style="text-decoration: underline;"> {$legalPayer['legalName']}, {$legalPayer['legalAddress']}</span><br/>
    <span>&nbsp;</span><br/>
    <span>Грузополучатель:</span><span style="text-decoration: underline;"> {$legalGetter['legalName']}, {$legalGetter['legalAddress']}</span>
</p>

<table border="1" style="width: 100%;">
    <tr>
        <td style="width: 30px; text-align: center; vertical-align: middle;">№</td>
        <td style="width: 320px; text-align: center;">Наименование товара</td>
        <td style="width: 50px; text-align: center;">Кол-во</td>
        <td style="width: 30px; text-align: center;">НДС</td>
        <td style="width: 50px; text-align: center;">Цена</td>
        <td style="width: 60px; text-align: center;">Сумма</td>
    </tr>
    $productRows
    <tr>
        <td style="width: 30px; text-align: center; vertical-align: middle;">$deliveryIndex</td>
        <td style=" text-align: center;">Доставка книжной продукции по заказу № {$this->params['status']['orderNumber']}</td>
        <td style="text-align: center;">1</td>
        <td style="text-align: center;">20</td>
        <td style="text-align: center;">{$this->params['delivery']['price']},00</td>
        <td style="text-align: center;">{$this->params['delivery']['price']},00</td>
    </tr>
    $ndsRows
</table>

<p><span>Всего наименований $deliveryIndex на сумму $totalPrice р.</span></p>

<p>
    <span>&nbsp;</span><br/>
    <span>Руководитель предприятия </span>
    <span style="text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <span> (Залипаев А.Г. довер. № ЦБ-13/04/23 от 13.04.23) </span><br/>
    <span>(подпись уполномоченного лица)</span><br/>
    
    <span>&nbsp;</span><br/>
    
    <span>Главный бухгалтер </span>
    <span style="text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <span> (Залипаев А.Г. довер. № ЦБ-13/04/23 от 13.04.23) </span><br/>
    <span>(подпись уполномоченного лица)</span><br/>
    
    <span>&nbsp;</span><br/>
    
    <span>Срок действия счета 5 дней</span><br/>
    <span>Стоимость и наличие товара по истечении 5 дней может измениться</span><br/>
</p>
EOD;

        $this->generator->writeHTMLCell(0, 0, '', '', $html, 0, 0, 0, true, '', true);
    }

    public function saveFile(string $fileName): bool
    {
        throw new UnknownMethodException('Method not realized!');
    }
}