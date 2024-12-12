<?php

namespace frontend\widgets\meta_tags;

use common\models\genre\GenreEntity;
use common\models\product\ProductEntity;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class MetaTagsWidget extends Widget
{
    const URL_TYPES = [
        '' => 'main',
        '/search' => 'search',
        '/promotion' => 'promotion',
        '/delivery' => 'delivery',
        '/contacts' => 'contacts',
        '/payment-refund' => 'paymentRefund',
        '/user-agreement' => 'userAgreement',
        '/product' => 'product',
        '/catalog' => 'catalog',
    ];

    const KEYWORDS = '';

    const LANGUAGES = [
        'Английский' => 'на английском языке',
        'Китайский' => 'на китайском языке',
        'Немецкий' => 'на немецком языке',
        'Французский' => 'на французском языке',
        'Испанский' => 'на испанском языке',
        'Итальянский' => 'на итальянском языке',
        'Арабский' => 'на арабском языке',
        'Японский' => 'на японском языке',
        'Иврит' => 'на иврите',
        'Греческий' => 'на греческом языке',
        'Финский' => 'на финском языке',
        'Латинский' => 'на латинском языке',
        'Корейский' => 'на корейском языке',
        'Чешский' => 'на чешском языке',
        'Польский' => 'на польском языке',
        'Турецкий' => 'на турецком языке',
    ];

    private string $originalUrl = '';
    private string $url = '';
    private string $type = '';
    private array $params = [];

    public bool $isVue = false;

    public function init()
    {
        $this->setUrl();
        $this->setType();
        $this->setParams();

        parent::init();
    }

    public function run(): string
    {
        return $this->render('index', $this->getMetaTags());
    }

    private function setParams()
    {
        $params = Yii::$app->request->get();
        unset($params['id']);

        $this->params = $params;
    }

    private function setUrl()
    {
        if (!$this->isVue) {
            preg_match_all('/\/[a-z0-9\-]+/', $_SERVER['REQUEST_URI'], $matches);
            $firstParam = $matches[0][0] ?? '';
            $secondParam = $matches[0][1] ?? '';

            $this->url = $firstParam;
            if ($secondParam === '/search') {
                $this->url = $secondParam;
            }
        }

        $this->originalUrl = 'https://deltabook.ru' . $this->url;

        if ($this->url === '/catalog' || $this->url ==='/product') {
            $this->originalUrl = 'https://deltabook.ru' . $this->url . $secondParam;
        }
    }

    private function setType(): void
    {
        foreach (self::URL_TYPES as $url => $type) {
            if ($url === $this->url) {
                $this->type = $type;
            }
        }
    }

    private function getMetaTags(): array
    {
        $method = "{$this->type}Template";

        if (!method_exists($this, $method)) {
            return $this->mainTemplate();
        }
        return $this->$method();
    }

    // region templates
    private function mainTemplate(): array
    {
        return [
            'title' => 'Deltabook.ru - интернет-магазин учебников для изучения иностранных языков',
            'description' => 'Интернет-магазин учебников и учебной литературы для изучения иностранных языков Дельтабук предлагает купить учебники английского, немецкого, французского, итальянского и испанского языка с доставкой по Москве и России.',
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => false,
        ];
    }

    private function searchTemplate(): array
    {
        return [
            'title' => 'Deltabook.ru - интернет-магазин учебников для изучения иностранных языков',
            'description' => 'Интернет-магазин учебников и учебной литературы для изучения иностранных языков Дельтабук предлагает купить учебники английского, немецкого, французского, итальянского и испанского языка с доставкой по Москве и России.',
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => true,
        ];
    }

    private function promotionTemplate(): array
    {
        return [
            'title' => 'Скидки и акции интернет-магазина Дельтабук',
            'description' => 'Совершайте покупки выгодно! На странице нашего интернет-магазина Deltabook.ru Вы можете ознакомиться со всеми действующими акциями и спецпредложениями!',
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => false,
        ];
    }

    private function deliveryTemplate(): array
    {
        return [
            'title' => 'Доставки и пункты выдачи интернет-магазина Дельтабук',
            'description' => 'Информация о доставке и пунктах выдачи интернет-магазина Deltabook.ru.',
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => false,
        ];
    }

    private function contactsTemplate(): array
    {
        return [
            'title' => 'Контакты интернет-магазина Дельтабук',
            'description' => 'Контактная информация интернет-магазина Deltabook.ru.',
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => false,
        ];
    }

    private function paymentRefundTemplate(): array
    {
        return [
            'title' => 'Способы оплаты и условия возврата в интернет-магазине Дельтабук',
            'description' => 'Способы оплаты заказов и условия возврата товаров в интернет-магазине Deltabook.ru.',
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => false,
        ];
    }

    private function userArgeementTemplate(): array
    {
        return [
            'title' => 'Пользовательское соглашение интернет-магазина Дельтабук',
            'description' => 'Пользовательское соглашение интернет-магазина Дельтабук.',
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => false,
        ];
    }

    private function productTemplate(): array
    {
        preg_match('/\/product\/([0-9]+)/', $_SERVER['REQUEST_URI'], $matches);
        $id = (int)$matches[1] ?? 0;

        if (!$id) {
            return $this->mainTemplate();
        }

        $productInfo = ProductEntity::find()
            ->select(['title', 'authors'])
            ->where(['id' => $id])
            ->asArray()
            ->one();

        return [
            'title' => "{$productInfo['title']} {$productInfo['authors']} - купить в интернет-магазине Дельтабук",
            'description' => "В интернет-магазине 📗 Deltabook.ru 📗 вы можете купить онлайн {$productInfo['title']} {$productInfo['authors']} по низкой цене. Интернет-магазин иностранной литературы с доставкой по Москве и России.",
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => false,
        ];
    }

    private function catalogTemplate():array
    {
        $genre = $this->view->context->genre ?? '';
        $productId = (int)ProductEntity::find()
            ->select(['products.id'])
            ->join(
                'JOIN',
                'product_genres',
                "product_genres.genre_id = {$genre->id} AND product_genres.product_id = products.id"
            )
            ->limit(1)
            ->scalar();

        $language = GenreEntity::find()
            ->select('genres.name')
            ->join(
                'JOIN',
                'product_genres',
                "genres.id = product_genres.genre_id AND product_genres.product_id = {$productId} AND level = 1")
            ->limit(1)
            ->scalar();

        if ($language === 'Другие языки') {
            $language = array_key_exists($genre->name, self::LANGUAGES) ? 'язык' :'языки';
        } else {
            $language = $language ? self::LANGUAGES[$language] : '';
        }

        if (($this->view->context->catalogType ?? '') == 'course') {
            return [
                'title' => "{$genre->name} – купить учебную литературу онлайн в Дельтабук",
                'description' => "В интернет-магазине 📗 Deltabook.ru 📗 вы можете купить онлайн учебники, рабочие тетради и пособия {$genre->name} по низкой цене. Интернет-магазин иностранной литературы с доставкой по Москве и России.",
                'keywords' => self::KEYWORDS,
                'canonical' => $this->originalUrl,
                'noindex' => !empty($this->params),
            ];
        }

        if (($this->view->context->catalogType ?? '') == 'courses') {
            return [
                'title' => "{$genre->name} {$language} – купить учебную литературу онлайн в Дельтабук",
                'description' => "В интернет-магазине 📗 Deltabook.ru 📗 вы можете купить онлайн учебники, рабочие тетради и пособия {$genre->name} {$language} по низкой цене. Интернет-магазин иностранной литературы с доставкой по Москве и России.",
                'keywords' => self::KEYWORDS,
                'canonical' => $this->originalUrl,
                'noindex' => !empty($this->params),
            ];
        }

        return [
            'title' => "{$genre->name} {$language} – купить книги онлайн в Дельтабук",
            'description' => "В интернет-магазине 📗 Deltabook.ru 📗 вы можете купить онлайн книги жанра {$genre->name} {$language} по низкой цене. Интернет-магазин иностранной литературы с доставкой по Москве и России.",
            'keywords' => self::KEYWORDS,
            'canonical' => $this->originalUrl,
            'noindex' => !empty($this->params),
        ];
    }
    // endregion
}