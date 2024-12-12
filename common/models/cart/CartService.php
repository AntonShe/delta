<?php

namespace common\models\cart;

use common\models\cart\CartRepository;
use common\models\product\ProductService;
use common\models\user\UserTrait;

class CartService
{
    use UserTrait;
    protected CartRepository $cartRepository;
    protected ProductService $productService;

    public function __construct()
    {
        $this->cartRepository = new CartRepository();
        $this->productService = new ProductService();
    }

    public function setParams(array $params): void
    {
        $this->cartRepository->setParams($params);
    }

    protected function setUserCartParams(): void
    {
        $params = $this->getUserParams();

        $this->cartRepository->mergeParams($params);
    }

    public function getCart(bool $needUserParams = true): array
    {
        if ($needUserParams) $this->setUserCartParams();

        $cart = $this->cartRepository->getCart();

        if (empty($cart)) $cart = $this->cartRepository->createCart();

        foreach ($cart['items'] as $id => &$item)
        {
            $this->productService->setParams([
                'id' => $item['product_id']
            ]);

            $product = $this->productService->getProducts()['products'];

            $item['isAvailable'] = $product[0]['active'] && $product[0]['quantity'] > 0;
            $item['quantity'] = [
                'available' => $product[0]['active'] ? $product[0]['quantity'] : 0,
                'cart' => $item['quantity']
            ];
            $item['productInfo'] =  $product[0];
            $item['productInfo']['price'] = [
                'old' => $product[0]['oldPrice'],
                'new' => $product[0]['price'],
                'discount' => $product[0]['oldPrice'] - $product[0]['price']
            ];

        }

        return $cart;
    }

    public function getCartItems(bool $needUserParams = true): array
    {
        if ($needUserParams) $this->setUserCartParams();

        $cart = $this->cartRepository->getCart();

        return $cart['items'] ?? [];
    }

    public function updateCart(): bool
    {
        return $this->cartRepository->updateCart();
    }

    public function getCartTotalCount(): int
    {
        $this->setUserCartParams();

        $cart = $this->cartRepository->getCart();

        if (isset($cart['items'])) {
            $count = 0;

            foreach ($cart['items'] as $item) {
                $count +=  $item['quantity'];
            }

            return $count;
        }

        return 0;
    }

    public function addToCart(): bool
    {
        $this->setUserCartParams();

        $cart = $this->cartRepository->getCart();

        if (empty($cart)) $cart = $this->cartRepository->createCart();

        if ($this->cartRepository->addToCart($cart['id'])) {
            return $this->recalculateCart();
        }

        return false;
    }

    public function recalculateCart(): bool
    {
        $cart = $this->cartRepository->getCart();
        $cart['raw_price'] = 0;
        $cart['final_price'] = 0;

        foreach ($cart['items'] as &$item)
        {
            $this->productService->setParams([
                'id' => $item['product_id']
            ]);

            $cart['raw_price'] += ($item['default_price'] * $item['quantity']);
            $cart['final_price'] += ($item['final_price'] * $item['quantity']);
        }

        $cart['discount_sum'] = $cart['raw_price'] - $cart['final_price'];

        $this->cartRepository->setParams($cart);

        return $this->cartRepository->updateCart();
    }

    public function setQuantity(): bool
    {
        if ($this->cartRepository->setQuantity()) {
            $this->setUserCartParams();

            return $this->recalculateCart();
        }

        return false;
    }

    public function deleteItems(): bool
    {
        if ($this->cartRepository->deleteItems()) {
            $this->setUserCartParams();

            return $this->recalculateCart();
        }

        return false;
    }

    public function deleteCart(): bool
    {
        return $this->cartRepository->deleteCart();
    }

    public function getItemId(array $params): int
    {
        if (empty($params)) return 0;

        $this->setUserCartParams();
        $cart = $this->cartRepository->getCart();

        return array_key_exists($params['productId'], $cart['items'])
            ? $cart['items'][$params['productId']]['id']
            : 0;
    }
}