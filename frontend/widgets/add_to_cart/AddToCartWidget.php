<?php

namespace frontend\widgets\add_to_cart;

use yii\base\Widget;

class AddToCartWidget extends Widget
{
    protected bool $status;
    protected array $books;
    protected int $cartId = 0;

    public ?int $id = 0;
    public ?string $title = '';
    public ?string $brand = '';
    public ?string $oldPrice = '';
    public ?string $price = '';
    public ?string $genres = '';
    public ?string $list_id = '';
    public ?string $list_name = '';
    public ?array $additionalClasses = [];
    public ?int $maxQuantity = 0;

    public function init()
    {
        parent::init();
        AddToCartAsset::register($this->view);
        $this->books = \Yii::$app->user->getCartBooks();
        $this->status = array_key_exists($this->id, $this->books);

        if ($this->status) {
            $this->cartId = $this->books[$this->id]['id'];
        }
    }

    public function run()
    {
        return $this->render('index', [
            'id' => $this->id,
            'title' => $this->title,
            'brand' => $this->brand,
            'oldPrice' => $this->oldPrice,
            'price' => $this->price,
            'genres' => $this->genres,
            'list_id' => $this->list_id,
            'list_name' => $this->list_name,
            'cartId' => $this->cartId,
            'status' => $this->status,
            'quantity' => ($this->books[$this->id]['quantity']['cart'] > $this->books[$this->id]['quantity']['available']
                ? $this->books[$this->id]['quantity']['available']
                : $this->books[$this->id]['quantity']['cart']) ?? 0,
            'isAvailable' => $this->books[$this->id]['isAvailable'],
            'maxQuantity' => $this->books[$this->id]['quantity']['available'] ?? $this->maxQuantity,
            'classes' => implode(' ', $this->additionalClasses)
        ]);
    }
}