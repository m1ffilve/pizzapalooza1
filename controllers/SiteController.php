<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RegistrationForm;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\RegisterForm;
use app\models\ChangePasswordForm;
use app\models\Pizza;
use app\models\PizzaForm;
use app\models\MenuForm;
use yii\helpers\FileHelper;
use app\models\RatingForm;
use yii\db\Expression;
use app\models\UserPizza;
use app\models\Promocode;
use app\models\Order;
use app\models\Promo;
use app\models\Payment;
use app\models\ReviewForm;
use app\models\Review;
use app\models\OrderedItem;
use yii\log\Logger;
use yii\web\BadRequestHttpException;
use app\models\PizzaRating;
use app\models\VacancyForm;
use app\models\Application;
use yii\swiftmailer\Message;
use yii\symfonymailer\MessageWrapperInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Mailgun\Mailgun;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() // Главная страницы
    {
        return $this->render('index');
    }
    public function actionVacancy()
    {
        $model = new Application(); // Создаем экземпляр модели Application

        return $this->render('vacancy', [
            'model' => $model, // Передаем модель в представление
        ]);
    }

    public function actionOnas() // О нас
    {
        return $this->render('onas');
    }
    public function actionCont() // Контакты
    {
        return $this->render('cont');
    }
    public function actionPayment() // Контакты
    {
        return $this->render('payment');
    }
    public function actionStocks() // Контакты
    {
        return $this->render('stocks');
    }
    public function actionRegister()
    {
        $regmodel = new RegisterForm();

        if ($regmodel->load(Yii::$app->request->post()) && $regmodel->register()) {
            // Найдите пользователя по его идентификатору, который вы получили при регистрации
            $user = User::findByPhoneNumber($regmodel->phone_number); // Используйте ваш метод для поиска пользователя

            if (Yii::$app->user->login($user)) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true]; // Возвращаем успешный ответ
            } else {
                // Если по каким-то причинам логин не удался
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => false, 'errors' => ['login' => 'Ошибка при входе в систему.']];
            }
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['success' => false, 'errors' => $regmodel->errors];
        }

        return $this->render('index', [
            'regmodel' => $regmodel,
        ]);
    }


    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $logmodel = new LoginForm();

        if ($logmodel->load(Yii::$app->request->post()) && $logmodel->login()) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['success' => true];
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['success' => false, 'errors' => $logmodel->errors];
        }

        return $this->render('index', ['model' => $logmodel]);
    }


    public function actionLogout()// выход
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }


    public function actionProfile()// профиль
    {
        // Получаем объект текущего пользователя
        $user = Yii::$app->user->identity;

        // Проверяем, что пользователь авторизован
        if (!$user) {
            // Обработка случая, когда пользователь не авторизован
            // Например, перенаправление на страницу авторизации
            return $this->redirect(['site/login']);
        }

        // Создаем объект модели пользователя для формы профиля
        $model = User::findOne($user->id);

        // Создаем объект модели промокода для формы добавления промокода
        $promoModel = new Promo();

        if (Yii::$app->request->isAjax && $promoModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($promoModel);
        }

        if ($promoModel->load(Yii::$app->request->post())) {
            // Проверка на уникальность кода промокода
            $existingPromo = Promo::findOne(['code' => $promoModel->code]);
            if ($existingPromo) {
                Yii::$app->session->setFlash('error', 'Промокод с таким кодом уже существует.');
            } else {
                $promoModel->save();
                Yii::$app->session->setFlash('success', 'Промокод успешно добавлен.');
                // Очистим поля формы после успешного сохранения
                $promoModel = new Promo();
            }
        }
        $dishes = Pizza::find()->all();
        return $this->render('profile', [
            'user' => $user,
            'model' => $model,
            'promoModel' => $promoModel,
            'dishes' => $dishes,
        ]);
    }


    public function actionEditProfile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
                $field = Yii::$app->request->post('field');
                $value = Yii::$app->request->post('value');

                $user = Yii::$app->user->identity;
                if ($user) {
                    $user->$field = $value;
                    if ($user->save()) {
                        return ['success' => true];
                    } else {
                        return ['success' => false, 'message' => 'Ошибка при сохранении данных пользователя.'];
                    }
                } else {
                    return ['success' => false, 'message' => 'Пользователь не авторизован.'];
                }
            } else {
                return ['success' => false, 'message' => 'Неверный тип запроса.'];
            }
        } catch (\Exception $e) {
            Yii::error('Ошибка обновления профиля пользователя: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Произошла ошибка при выполнении запроса.'];
        }
    }
    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = Yii::$app->user->identity;
            $user->password_hash = Yii::$app->security->generatePasswordHash($model->newPassword);
            if ($user->save(false)) {
                return $this->asJson(['success' => true, 'message' => 'Пароль успешно изменен.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'Не удалось сменить пароль.']);
            }
        }

        return $this->asJson([
            'success' => false,
            'message' => 'Некорректные данные.',
            'errors' => $model->errors
        ]);
    }

    // В SiteController
    public function actionPizza()
    {
        $newPizzas = Pizza::find()->where(['category' => 'pizza'])->orderBy(['created_at' => SORT_DESC])->limit(30)->all();
        $popularPizzas = Pizza::find()->where(['category' => 'pizza'])->orderBy(['rating' => SORT_DESC])->limit(30)->all();

        // Вычисляем средний рейтинг для каждой пиццы
        $averageRatings = [];
        foreach ($popularPizzas as $pizza) {
            $averageRatings[$pizza->id] = $pizza->calculateRating();
        }

        return $this->render('pizza', [
            'newPizzas' => $newPizzas,
            'popularPizzas' => $popularPizzas,
            'averageRatings' => $averageRatings, // Передаем средние рейтинги в представление
        ]);
    }



    public function actionZakuski()
    {
        // Получаем все пиццы в категории 'zakuski'
        $allPizzas = Pizza::find()->where(['category' => 'zakuski'])->all();

        // Вычисляем средний рейтинг для каждой пиццы
        $averageRatings = [];
        foreach ($allPizzas as $pizza) {
            $averageRatings[$pizza->id] = $pizza->calculateRating();
        }

        // Сортируем пиццы по дате создания и рейтингу
        $newPizzas = Pizza::find()->where(['category' => 'zakuski'])->orderBy(['created_at' => SORT_DESC])->limit(30)->all();
        $popularPizzas = Pizza::find()->where(['category' => 'zakuski'])->orderBy(['rating' => SORT_DESC])->limit(30)->all();

        return $this->render('zakuski', [
            'newPizzas' => $newPizzas,
            'popularPizzas' => $popularPizzas,
            'averageRatings' => $averageRatings,
        ]);
    }
    public function actionDeserti()// десерты
    {
        $newPizzas = Pizza::find()->where(['category' => 'deserti'])->orderBy(['created_at' => SORT_DESC])->limit(30)->all();
        $popularPizzas = Pizza::find()->where(['category' => 'deserti'])->orderBy(['rating' => SORT_DESC])->limit(30)->all();
        $averageRatings = [];
        foreach ($popularPizzas as $pizza) {
            $averageRatings[$pizza->id] = $pizza->calculateRating();
        }
        return $this->render('deserti', [
            'newPizzas' => $newPizzas,
            'popularPizzas' => $popularPizzas,
            'averageRatings' => $averageRatings,
        ]);
    }
    public function actionNapitki()// напитки
    {
        $newPizzas = Pizza::find()->where(['category' => 'napitki'])->orderBy(['created_at' => SORT_DESC])->limit(30)->all();
        $popularPizzas = Pizza::find()->where(['category' => 'napitki'])->orderBy(['rating' => SORT_DESC])->limit(30)->all();
        $averageRatings = [];
        foreach ($popularPizzas as $pizza) {
            $averageRatings[$pizza->id] = $pizza->calculateRating();
        }
        return $this->render('napitki', [
            'newPizzas' => $newPizzas,
            'popularPizzas' => $popularPizzas,
            'averageRatings' => $averageRatings,
        ]);
    }
    public function actionSousi()// соусы
    {
        $newPizzas = Pizza::find()->where(['category' => 'sousi'])->orderBy(['created_at' => SORT_DESC])->limit(30)->all();
        $popularPizzas = Pizza::find()->where(['category' => 'sousi'])->orderBy(['rating' => SORT_DESC])->limit(30)->all();
        $averageRatings = [];
        foreach ($popularPizzas as $pizza) {
            $averageRatings[$pizza->id] = $pizza->calculateRating();
        }
        return $this->render('sousi', [
            'newPizzas' => $newPizzas,
            'popularPizzas' => $popularPizzas,
            'averageRatings' => $averageRatings,
        ]);
    }
    public function actionAddMenu()// добавить меню
    {
        $model = new MenuForm();
        $categoriesWithTranslations = Pizza::getCategoriesWithTranslations();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->validate()) {
                $uploadPath = 'uploads/';
                // Создание директории, если её нет
                FileHelper::createDirectory($uploadPath);
                $filePath = $uploadPath . $model->imageFile->baseName . '.' . $model->imageFile->extension;
                // Сохранение изображения в папку
                if ($model->imageFile->saveAs($filePath)) {
                    // Добавление остальных данных в базу данных
                    $menu = new Pizza(); // Замените на вашу модель
                    $menu->name = $model->name;
                    $menu->category = $model->category;
                    $menu->price = $model->price;
                    $menu->composition = $model->composition;
                    $menu->cook_time = $model->cook_time;
                    $menu->weight = $model->weight;
                    $menu->size = $model->size;
                    $menu->history = $model->history;
                    $menu->image_url = $filePath; // Сохраняем путь к файлу в базу данных
                    if ($menu->save()) {
                        return $this->redirect(['site/index']);
                    } else {
                        Yii::$app->session->setFlash('error', 'Не удалось сохранить данные в базу данных.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось загрузить файл.');
                }
                if ($menu->save()) {
                    // Устанавливаем начальное значение рейтинга при создании записи
                    $menu->updateAttributes(['rating' => 0]);

                    return $this->redirect(['site/index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось сохранить данные в базу данных.');
                }
            }
        }
        return $this->render('add-menu', [
            'model' => $model,
            'categories' => $categoriesWithTranslations, // Передаем категории в представление
        ]);
    }
    // Метод в модели Pizza для получения всех уникальных категорий

    public function actionRatePizza()// рейтинг
    {
        $pizzaId = Yii::$app->request->post('pizzaId');
        $starClicked = Yii::$app->request->post('starClicked');
        Yii::info("Received rating update request for pizza ID $pizzaId with rating $starClicked");
        $pizza = Pizza::findOne($pizzaId);
        if ($pizza) {
            // Обновление рейтинга в базе данных
            $pizza->updateAttributes(['rating' => $starClicked]);

            // Пересчитываем средний рейтинг
            $averageRating = $this->actionCalculateAverageRating($pizzaId);

            return Yii::$app->response->data = ['success' => true, 'averageRating' => $averageRating];
        } else {
            Yii::error("Pizza not found for ID $pizzaId");
            return Yii::$app->response->data = ['success' => false, 'error' => 'Pizza not found'];
        }
    }
    public function actionAddToCart()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $pizzaId = Yii::$app->request->post('id');
        $pizzaName = Yii::$app->request->post('name');
        $pizzaPrice = Yii::$app->request->post('price');
        Yii::info("Received addToCart request for pizza ID $pizzaId, name: $pizzaName, price: $pizzaPrice");

        if (Yii::$app->user->isGuest) {
            return [
                'success' => false,
                'message' => 'Пожалуйста, зарегистрируйтесь или войдите в аккаунт.'
            ];
        }

        // Получение текущего пользователя
        $userId = Yii::$app->user->id;

        // Получение корзины пользователя из таблицы user_pizza
        $userPizza = UserPizza::find()
            ->where(['user_id' => $userId, 'pizza_id' => $pizzaId])
            ->one();

        if ($userPizza) {
            // Если блюдо уже есть в корзине, увеличиваем количество
            $userPizza->quantity += 1;
        } else {
            // Иначе создаем новую запись
            $userPizza = new UserPizza([
                'user_id' => $userId,
                'pizza_id' => $pizzaId,
                'quantity' => 1,
                'total_cost' => $pizzaPrice // Инициализируем total_cost
            ]);
        }

        $userPizza->total_cost = $userPizza->quantity * $pizzaPrice;

        if ($userPizza->save()) {
            // Обновление количества товаров в корзине
            $cartItemCount = UserPizza::find()->where(['user_id' => $userId])->sum('quantity');
            Yii::$app->session->set('cartItemCount', $cartItemCount);

            return [
                'success' => true,
                'cartItemCount' => $cartItemCount
            ];
        } else {
            Yii::error("Failed to save userPizza for pizza ID $pizzaId");
            return [
                'success' => false,
                'message' => 'Не удалось добавить товар в корзину.'
            ];
        }
    }


    public function actionCart()
    {
        // Получаем ID текущего пользователя
        $userId = Yii::$app->user->id;

        // Ищем все записи корзины для данного пользователя
        $cart = UserPizza::find()->where(['user_id' => $userId])->all();
        $orders = Order::find()->where(['user_id' => $userId])->all();
        // Рассчитываем исходную сумму заказа без учета скидок
        $totalCost = $this->calculateTotalCost($userId);
        $cartIsEmpty = empty($cart);
        // Получаем скидочную стоимость из сессии, если она есть
        $discountedCost = Yii::$app->session->get('discountedCost', $totalCost);

        // Проверяем, применен ли промокод
        $isPromoApplied = Yii::$app->session->has('promoCode');

        // Рассчитываем общую стоимость с учетом примененной скидки, если промокод был применен
        $finalCost = $isPromoApplied ? $discountedCost : $totalCost;

        // Передаем информацию о корзине, общей стоимости, скидке и промокоде в представление
        return $this->render('cart', [
            'cart' => $cart,
            'totalCost' => $totalCost,
            'discountedCost' => $discountedCost,
            'isPromoApplied' => $isPromoApplied,
            'finalCost' => $finalCost,
            'orders' => $orders,
            'userId' => $userId,
            'cartIsEmpty' => $cartIsEmpty,
        ]);
    }


    public function actionApplyPromoCode()
    {
        $response = ['success' => false, 'message' => 'Промокод не найден.'];

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $code = Yii::$app->request->post('promoCode');
            $promo = Promo::findOne(['code' => $code]);

            if ($promo) {
                $userId = Yii::$app->user->id;
                $totalCost = $this->calculateTotalCost($userId);
                $discount = $promo->discount;
                $discountedCost = round($totalCost * (1 - $discount / 100), 2);

                // Обновляем сумму без скидки в сессии
                Yii::$app->session->set('totalCost', $totalCost);

                Yii::$app->session->set('promoCode', $code);
                Yii::$app->session->set('discountedCost', $discountedCost);

                // Обновляем общую стоимость заказа в базе данных
                $this->updateUserTotalCost($userId, $discountedCost); // Передаем скидку для обновления общей суммы с учетом промокода

                $response = [
                    'success' => true,
                    'message' => 'Промокод успешно применен.',
                    'discountedCost' => $discountedCost,
                    'totalCost' => $totalCost,
                ];
            }
        }

        return $response;
    }


    public function actionUpdateQuantity()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $pizzaId = Yii::$app->request->post('pizzaId');
        $action = Yii::$app->request->post('action');

        // Находим конкретный товар в корзине пользователя
        $cartItem = UserPizza::findOne(['user_id' => Yii::$app->user->id, 'pizza_id' => $pizzaId]);
        if (!$cartItem) {
            return ['success' => false];
        }

        // В зависимости от действия (увеличение или уменьшение), обновляем количество товара
        if ($action === 'increase') {
            $cartItem->quantity++;
        } elseif ($action === 'decrease' && $cartItem->quantity > 1) {
            $cartItem->quantity--;
        }

        // Сохраняем изменения
        $cartItem->save();

        // Пересчитываем сумму товара
        $itemTotalPrice = $cartItem->pizza->price * $cartItem->quantity;

        // Пересчитываем общую сумму заказа
        $totalCost = $this->calculateTotalCost(Yii::$app->user->id);

        Yii::$app->session->set('totalCost', $totalCost);

        // Обновляем сумму с промокодом
        $discountedCost = Yii::$app->session->get('discountedCost', $totalCost);
        $isPromoApplied = Yii::$app->session->has('promoCode');

        // Если промокод применен, обновляем его сумму
        if ($isPromoApplied) {
            $code = Yii::$app->session->get('promoCode');
            $promo = Promo::findOne(['code' => $code]);
            if ($promo) {
                $discount = $promo->discount;
                $discountedCost = round($totalCost * (1 - $discount / 100), 2);
                Yii::$app->session->set('discountedCost', $discountedCost);
                $this->updateUserTotalCost(Yii::$app->user->id, $discountedCost); // Обновляем общую сумму с учетом промокода
            }
        }

        // Возвращаем обновленные данные
        return [
            'success' => true,
            'quantity' => $cartItem->quantity,
            'itemTotalPrice' => $itemTotalPrice,
            'totalCost' => $totalCost,
            'isPromoApplied' => $isPromoApplied,
            'discountedCost' => $discountedCost,
        ];
    }

    public function actionGetOriginalTotalCost()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->user->id;
        $totalCost = $this->calculateTotalCost($userId);

        return [
            'success' => true,
            'originalTotalCost' => $totalCost,
        ];
    }

    public function actionRemoveItemFromCart()
    {
        // Получаем значение параметра pizzaId из POST запроса
        $pizzaId = Yii::$app->request->post('pizzaId');

        // Логируем значение полученного параметра
        Yii::info('Received pizzaId: ' . $pizzaId);

        // Проверяем, что параметр pizzaId передан корректно
        if ($pizzaId === null) {
            // Если pizzaId не был передан, возвращаем ошибку 400
            Yii::error('Missing required parameter: pizzaId');
            throw new BadRequestHttpException('Missing required parameter: pizzaId');
        }

        // Получаем текущего пользователя
        $userId = Yii::$app->user->id;

        // Получаем текущую сумму заказа без учета промокода
        $totalCost = $this->calculateTotalCost($userId);

        // Находим запись корзины с соответствующим pizzaId
        $cartItem = UserPizza::findOne(['pizza_id' => $pizzaId]);

        if (!$cartItem) {
            // Если запись корзины не найдена, возвращаем ошибку 404
            Yii::error('Cart item not found for pizzaId: ' . $pizzaId);
            throw new NotFoundHttpException('Cart item not found for pizzaId: ' . $pizzaId);
        }

        // Получаем цену товара, который удаляем из корзины
        $itemPrice = $cartItem->total_cost * $cartItem->quantity;

        // Удаляем запись корзины
        $cartItem->delete();

        // Получаем обновленную сумму заказа без учета промокода после удаления товара
        $updatedTotalCost = $this->calculateTotalCost($userId);

        // Если промокод применен, обновляем его сумму
        $isPromoApplied = Yii::$app->session->has('promoCode');
        if ($isPromoApplied) {
            $code = Yii::$app->session->get('promoCode');
            $promo = Promo::findOne(['code' => $code]);
            if ($promo) {
                $discount = $promo->discount;
                $discountedCost = round($updatedTotalCost * (1 - $discount / 100), 2);
                Yii::$app->session->set('discountedCost', $discountedCost);
                $this->updateUserTotalCost($userId, $discountedCost); // Обновляем общую сумму с учетом промокода
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => true,
            'itemPrice' => $itemPrice,
            'totalCost' => $updatedTotalCost,
            'discountedCost' => Yii::$app->session->get('discountedCost', $totalCost), // Обновленная сумма с промокодом
            'isPromoApplied' => $isPromoApplied,
            'originalTotalCost' => $totalCost, // Обновленная общая сумма без учета промокода
        ];
    }

    public function actionDeleteOrder()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $data = json_decode(file_get_contents('php://input'), true);
        $orderId = isset($data['orderId']) ? $data['orderId'] : null;

        if (!$orderId) {
            return ['success' => false, 'message' => 'Идентификатор заказа не передан.'];
        }

        $order = Order::findOne($orderId);

        if (!$order) {
            return ['success' => false, 'message' => 'Заказ не найден.'];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Удаляем записи из ordered_items, связанные с этим заказом
            OrderedItem::deleteAll(['order_id' => $orderId]);

            // Удаляем сам заказ
            if ($order->delete()) {
                $transaction->commit();
                return ['success' => true];
            } else {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'Ошибка при удалении заказа.'];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Произошла ошибка: ' . $e->getMessage()];
        }
    }
    protected function calculateTotalCost($userId)
    {
        $cartItems = UserPizza::find()->where(['user_id' => $userId])->all();
        $totalCost = 0;
        foreach ($cartItems as $item) {
            $totalCost += $item->total_cost * $item->quantity; // Предполагается, что у модели UserPizza есть поля total_cost и quantity
        }
        return $totalCost;
    }

    protected function updateUserTotalCost($userId, $totalAmount)
    {
        $user = User::findOne($userId);
        if ($user) {
            $user->total_cost += $totalAmount; // Увеличиваем общую стоимость на сумму заказа
            $user->save();
        }
    }


    public function actionClearCart()
    {
        // Ваш код для очистки корзины
        $userId = Yii::$app->user->id;
        UserPizza::deleteAll(['user_id' => $userId]);

        // Очищаем данные сессии, если используются
        Yii::$app->session->remove('promoCode');
        Yii::$app->session->remove('discountedCost');
        Yii::$app->session->set('cartItemCount', 0);
        // Перенаправляем пользователя обратно на страницу корзины или куда-либо еще
        return $this->redirect(['site/cart']); // Предполагается, что есть действие для страницы корзины
    }
    public function beforeAction($action)
    {
        if ($action->id == 'update-quantity') {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['create-order'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['clear-cart'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['apply-promo-code'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['update-order-status-every-ten-seconds'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['update-cart-item'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['remove-item-from-cart'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['save-rating'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['take-order'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['submit-application'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['add-to-cart'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['update-dish'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['repeat-order'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['reply'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['delete-review'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['delete-order'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['edit-profile'])) {
            $this->enableCsrfValidation = false;
        }
        if (in_array($action->id, ['change-password'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
    public function actionGetCart()// получение данных в корзину
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Получение текущего пользователя
        $userId = Yii::$app->user->id;

        // Получение корзины пользователя из таблицы user_pizza
        $cart = UserPizza::find()
            ->select(['pizza_id', 'quantity'])
            ->where(['user_id' => $userId])
            ->asArray()
            ->all();
        $cartData = [];
        foreach ($cart as $item) {
            $pizza = Pizza::findOne($item['pizza_id']);
            if ($pizza) {
                $cartData[] = [
                    'pizza_id' => $item['pizza_id'],
                    'quantity' => $item['quantity'],
                    'price' => $pizza->price,
                    'pizza' => $pizza, // Включаем информацию о pizza
                    // Добавьте другие поля по мере необходимости
                ];
            } else {
                Yii::error('Pizza not found for pizza_id: ' . $item['pizza_id']);
            }
        }
        return $cartData;
    }
    public function actionAddPromo()// добавление промо
    {
        $promoModel = new Promo();
        if (Yii::$app->request->isAjax && $promoModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($promoModel);
        }
        if ($promoModel->load(Yii::$app->request->post()) && $promoModel->validate()) {
            $promoModel->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->renderAjax('profile', [
            'promoModel' => $promoModel,
        ]);
    }
    public function actionCreateOrder()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Устанавливаем формат ответа
        $request = Yii::$app->request;
        // Создание нового заказа
        $order = new Order();
        $order->user_id = $request->post('user_id');
        $order->name = $request->post('name');
        $order->phone = $request->post('phone');
        $order->email = $request->post('email');
        $order->address = $request->post('address');
        $order->comment = $request->post('comment');
        $order->card_number = $request->post('card_number');
        $order->card_expiry = $request->post('card_expiry');
        $order->card_cvv = $request->post('card_cvv');
        $order->payment_method = $request->post('paymentMethod');
        $order->delivery_method = $request->post('deliveryMethod');
        $order->created_at = date('Y-m-d H:i:s');
        $order->status = Order::STATUS_NEW;
        $totalCost = Yii::$app->session->get('orderTotalCost', 0);
        $order->total_amount = $totalCost;
        // Сохранение заказа
        if ($order->save()) {
            // Получение товаров из корзины пользователя
            $userPizzas = UserPizza::find()->where(['user_id' => Yii::$app->user->id])->all();
            foreach ($userPizzas as $userPizza) {
                // Создание новой записи в таблице ordered_items для каждого товара в корзине
                $orderedItem = new OrderedItem();
                $orderedItem->order_id = $order->id;
                $orderedItem->pizza_id = $userPizza->pizza_id;
                $orderedItem->save();
            }
            // Очистка корзины пользователя
            UserPizza::deleteAll(['user_id' => Yii::$app->user->id]);
            // Возвращаем успешный ответ с ID заказа
            return ['success' => true, 'orderId' => $order->id];

        } else {
            Yii::$app->session->setFlash('applicationnonsSubmitted', true);
            // Возвращаем сообщение об ошибке, если сохранение заказа не удалось
            return ['success' => false, 'message' => 'Ошибка при сохранении заказа'];
        }
    }


    public function actionCheckOrderExists()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $orderCount = Order::find()->count();
        return ['exists' => $orderCount > 0];
    }
    public function actionGetOrderStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;
        $order = Order::find()->orderBy(['created_at' => SORT_DESC])->one();
        $status = $order ? $order->status : Order::STATUS_NEW;
        return ['status' => $status];
    }
    public function actionUpdateOrderStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = Yii::$app->user->id;
        $request = Yii::$app->request;

        $status = $request->post('status');
        $orderId = $request->post('orderId'); // Получаем ID заказа из параметра запроса
        $order = Order::findOne($orderId);
        if (!$order) {
            Yii::error('Заказ не найден', 'orders');
            return ['success' => false, 'message' => 'Заказ не найден'];
        }
        // Проверяем, что переданный статус соответствует одному из четырех этапов
        // Проверяем, что переданный статус соответствует одному из четырех этапов
        if ($status === 'new' || $status === 'processing' || $status === 'completed' || $status === 'picked_up') {
            $order->status = $status;
            if ($order->save()) {
                Yii::info('Статус заказа успешно обновлен', 'orders');
                return ['success' => true, 'message' => 'Статус заказа успешно обновлен', 'status' => $status];
            } else {
                Yii::error('Ошибка при обновлении статуса заказа: ' . print_r($order->errors, true), 'orders');
                return ['success' => false, 'message' => 'Ошибка при обновлении статуса заказа', 'errors' => $order->errors];
            }
        } else {
            Yii::error('Неверный статус заказа: ' . $status, 'orders');
            return ['success' => false, 'message' => 'Неверный статус заказа'];
        }
    }

    public function actionUpdateOrderStatusEveryTenSeconds()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // Получаем последний заказ
        $order = Order::find()->orderBy(['created_at' => SORT_DESC])->one();
        if ($order) {
            // Проверяем текущий статус заказа
            switch ($order->status) {
                case Order::STATUS_NEW:
                    // Если текущий статус заказа "новый", меняем на "в процессе"
                    $order->status = Order::STATUS_PROCESSING;
                    break;
                case Order::STATUS_PROCESSING:
                    // Если текущий статус заказа "в процессе", меняем на "завершен"
                    $order->status = Order::STATUS_COMPLETED;
                    break;
                case Order::STATUS_COMPLETED:
                    // Если текущий статус заказа "завершен", не меняем статус
                    break;
            }
            // Сохраняем изменения
            if ($order->save()) {
                // Возвращаем новый статус заказа
                return ['success' => true, 'status' => $order->status];
            } else {
                return ['success' => false, 'message' => 'Ошибка при обновлении статуса заказа', 'errors' => $order->errors];
            }
        } else {
            return ['success' => false, 'message' => 'Заказ не найден'];
        }
    }
    public function actionReview()
    {
        $userId = Yii::$app->user->id;
        $lastOrder = Order::find()
            ->joinWith('reviews') // Присоединяем связь с отзывами
            ->where(['orders.user_id' => $userId]) // Уточняем, что user_id принадлежит таблице orders
            ->orderBy(['orders.created_at' => SORT_DESC]) // Уточняем поле created_at
            ->one();

        $role = null;
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            if ($user !== null && isset($user->role)) {
                $role = $user->role;
            }
        }

        // Получаем все товары для последнего заказа пользователя
        $orderedItems = $lastOrder ? $lastOrder->orderedItems : [];
        // Создаем модель для формы отзыва
        $reviewModel = new ReviewForm();
        $reviews = Review::find()->all();

        return $this->render('review', [
            'lastOrder' => $lastOrder,
            'reviewModel' => $reviewModel,
            'reviews' => $reviews,
            'orderedItems' => $orderedItems,
            'role' => $role,
        ]);
    }



    public function actionAddReview()
    {
        $model = new ReviewForm();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->user_id = Yii::$app->user->id;
            if ($model->validate()) {
                if ($model->saveReview()) {
                    Yii::$app->session->setFlash('success', 'Отзыв успешно добавлен.');
                } else {
                    Yii::$app->session->setFlash('error', 'Произошла ошибка при сохранении отзыва.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Проверьте правильность заполнения формы.');
            }
        }
        // Используем свойство order_id из модели ReviewForm
        return $this->redirect(['site/review']);
    }
    public function actionSaveRating()
    {
        $request = Yii::$app->request;
        $pizzaId = $request->post('pizzaId');
        $rating = $request->post('rating');

        // Получаем текущего пользователя, если он аутентифицирован
        $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;

        // Проверяем, существует ли запись о рейтинге для данной пиццы и данного пользователя
        $pizzaRating = PizzaRating::find()->where(['pizza_id' => $pizzaId, 'user_id' => $userId])->one();

        if ($pizzaRating) {
            // Если запись существует, обновляем рейтинг
            $pizzaRating->rating = $rating;
            $pizzaRating->save();
        } else {
            // Если записи нет, создаем новую запись
            $pizzaRating = new PizzaRating();
            $pizzaRating->pizza_id = $pizzaId;
            $pizzaRating->rating = $rating;
            $pizzaRating->user_id = $userId; // Устанавливаем ID пользователя или NULL
            $pizzaRating->save();
        }

        // Получаем и возвращаем обновленный рейтинг в формате JSON
        $updatedRating = $this->getRating($pizzaId);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['success' => true, 'rating' => $updatedRating];
    }

    public function actionGetRating($pizzaId)
    {
        // Получаем рейтинг пиццы
        $rating = $this->getRating($pizzaId);

        // Возвращаем рейтинг в формате JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['rating' => $rating];
    }


    // Вспомогательная функция для получения рейтинга пиццы
// Вспомогательная функция для получения рейтинга пиццы
    private function getRating($pizzaId)
    {
        // Находим пиццу по ее ID
        $pizzaRating = PizzaRating::find()
            ->select('AVG(rating) AS rating')
            ->where(['pizza_id' => $pizzaId])
            ->scalar();

        // Округляем рейтинг до одного знака после запятой
        $roundedRating = round($pizzaRating, 1);

        return $roundedRating !== null ? $roundedRating : 0;
    }
    public function actionTakeOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Проверяем, был ли отправлен POST-запрос
        if (Yii::$app->request->isPost) {
            // Получаем идентификатор заказа из тела запроса
            $orderId = Yii::$app->request->getBodyParam('orderId');

            // Здесь ваша реальная логика для обновления статуса заказа на "забран"
            // Например, обновление статуса заказа в базе данных
            $order = Order::findOne($orderId);
            if ($order !== null) {
                $order->status = 'picked_up'; // Устанавливаем статус "забран"
                $order->save();

                // Возвращаем успешный ответ
                return $this->redirect(['site/cart']);
            } else {
                // Если заказ не найден, возвращаем ошибку
                return ['success' => false, 'error' => 'Order not found'];
            }
        }

        // Если запрос не является POST-запросом, возвращаем ошибку
        throw new BadRequestHttpException('Only POST requests are allowed for this action.');
    }

    public function actionSubmitApplication()
    {
        $model = new Application();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $uploadedResume = UploadedFile::getInstance($model, 'resume');
            if ($uploadedResume) {
                $path = 'uploads/' . $uploadedResume->baseName . '.' . $uploadedResume->extension;
                $model->resume_path = $path;
            }

            if ($model->validate()) {
                Yii::info('Данные успешно загружены в модель', 'application');
                if ($model->save()) {
                    if ($uploadedResume) {
                        $uploadedResume->saveAs($path);
                    }
                    Yii::info('Заявление успешно сохранено в базе данных', 'application');
                    Yii::$app->session->setFlash('applicationSubmitted', true);
                    return $this->redirect(['site/vacancy']);
                } else {
                    Yii::error('Произошла ошибка при сохранении заявления в базе данных', 'application');
                    Yii::$app->session->setFlash('applicationnonSubmitted', true);
                }
            } else {
                Yii::warning('Произошла ошибка в валидации данных', 'application');
                Yii::$app->session->setFlash('applicationnonSubmitted', true);
            }
        }

        // Если данные не прошли валидацию, возвращаем представление с формой и ошибками
        return $this->render('vacancy', [
            'model' => $model,
        ]);
    }



    public function actionApplAnswer()
    {
        $vacancies = Application::find()->all(); // Получаем все вакансии из базы данных
        return $this->render('appl-answer', ['vacancies' => $vacancies]); // Передаем в представление список всех вакансий
    }
    public function actionViewVacancy($id)
    {
        $vacancy = Application::findOne($id); // Получаем информацию о вакансии по идентификатору
        if (!$vacancy) {
            throw new \yii\web\NotFoundHttpException('Вакансия не найдена.');
        }

        return $this->render('view-vacancy', ['vacancy' => $vacancy]); // Передаем в представление информацию о вакансии
    }
    public function actionViewProduct($id)
    {
        $product = Pizza::findOne($id);
        if ($product === null) {
            throw new \yii\web\NotFoundHttpException("Товар не найден");
        }

        return $this->render('view-product', [
            'product' => $product,
        ]);
    }
    // Экшен для страницы статистики
    public function actionStats()
    {
        // Получаем общее количество заказов
        $ordersCount = Yii::$app->db->createCommand('SELECT COUNT(*) FROM orders')->queryScalar();

        // Получаем список заказов
        $orders = Order::find()->all();

        // Получаем общее количество пользователей
        $usersCount = Yii::$app->db->createCommand('SELECT COUNT(*) FROM user')->queryScalar();

        // Получаем общее количество продуктов
        $productsCount = Yii::$app->db->createCommand('SELECT COUNT(*) FROM pizza')->queryScalar();

        // Запрашиваем топ-5 популярных блюд (пицц) на основе рейтинга из таблицы pizza_rating
        $topDishes = PizzaRating::find()
            ->select(['pizza_id', 'AVG(rating) AS avg_rating'])
            ->groupBy('pizza_id')
            ->orderBy(['avg_rating' => SORT_DESC])
            ->limit(5)
            ->with('pizza') // Загружаем связанные данные о пицце
            ->all();

        // Получаем список всех пользователей
        $users = User::find()->all();

        // Передаем данные в представление stats.php для отображения
        return $this->render('stats', [
            'ordersCount' => $ordersCount,
            'usersCount' => $usersCount,
            'productsCount' => $productsCount,
            'topDishes' => $topDishes,
            'orders' => $orders, // Передаем список заказов в представление
            'users' => $users, // Передаем список пользователей в представление
        ]);
    }

    public function actionGetCartItemCount()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Получаем количество товаров в корзине из базы данных или сессии
        $cartItemCount = UserPizza::find()->where(['user_id' => Yii::$app->user->id])->sum('quantity');

        return ['cartItemCount' => $cartItemCount];
    }
    public function actionGetDishDetails($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $dish = Pizza::findOne($id);
        if ($dish) {
            return $dish;
        } else {
            return ['status' => 'error', 'message' => 'Блюдо не найдено.'];
        }
    }

    public function actionUpdateDish()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if ($request->isPost) {
            $dishId = $request->post('id');
            $dish = Pizza::findOne($dishId);

            if ($dish) {
                $dish->name = $request->post('name');
                $dish->composition = $request->post('composition');
                $dish->price = $request->post('price');
                $dish->history = $request->post('history');
                // Добавьте другие поля, которые вы хотите обновить
                if ($dish->save()) {
                    return ['status' => 'success', 'message' => 'Блюдо успешно обновлено.'];
                } else {
                    return ['status' => 'error', 'message' => 'Ошибка при сохранении данных.', 'errors' => $dish->getErrors()];
                }
            } else {
                return ['status' => 'error', 'message' => 'Блюдо не найдено.'];
            }
        }

        return ['status' => 'error', 'message' => 'Неверный запрос.'];
    }
    public function actionGetOrderHistory()
    {
        // Получаем идентификатор текущего пользователя
        $userId = Yii::$app->user->getId();

        // Получаем историю заказов пользователя из базы данных
        $orders = Order::find()->where(['user_id' => $userId])->all();

        // Формируем массив данных о заказах
        $orderHistory = [];
        foreach ($orders as $order) {
            $totalAmount = 0;
            $orderedItems = $order->orderedItems;
            $itemNames = [];

            foreach ($orderedItems as $item) {
                $pizza = Pizza::findOne($item->pizza_id);
                if ($pizza !== null) {
                    $itemNames[] = $pizza->name;
                    $totalAmount += $pizza->price;
                }
            }

            // Для каждого заказа добавляем нужные данные в массив
            $orderHistory[] = [
                'id' => $order->id,
                'createdAt' => $order->created_at,
                'total' => $totalAmount,
                'itemNames' => $itemNames, // Добавляем массив имен товаров в заказе
            ];
        }

        // Возвращаем данные в формате JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $orderHistory;
    }

    public function actionRepeatOrder()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $orderId = $data['orderId'];

        // Находим заказ по его идентификатору
        $order = Order::findOne($orderId);

        if ($order) {
            // Создаем новый заказ на основе старого
            $newOrder = new Order();
            $newOrder->attributes = $order->attributes;

            // Устанавливаем новый идентификатор заказа
            $newOrder->id = null;
            $newOrder->user_id = $order->user_id;
            // Сохраняем новый заказ
            if ($newOrder->save()) {
                // Успешно создан новый заказ

                // Добавляем товары из повторяемого заказа в таблицу ordered_items
                foreach ($order->orderedItems as $orderedItem) {
                    $newOrderedItem = new OrderedItem();
                    $newOrderedItem->order_id = $newOrder->id;
                    $newOrderedItem->pizza_id = $orderedItem->pizza_id;
                    $newOrderedItem->save();
                }

                // Обновляем статус нового заказа на STATUS_NEW
                $newOrder->status = Order::STATUS_NEW;
                $newOrder->save();

                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['status' => 'success', 'message' => 'Order repeated successfully.'];
            } else {
                // Ошибка при сохранении нового заказа
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['status' => 'error', 'message' => 'Failed to repeat order.'];
            }
        } else {
            // Заказ не найден
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['status' => 'error', 'message' => 'Order not found.'];
        }
    }
    public function actionReply()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $postData = Yii::$app->request->post();
        Yii::info("POST Data: " . json_encode($postData), __METHOD__);

        $reviewId = $postData['reviewId'] ?? null;
        $adminReply = $postData['adminReply'] ?? null;

        if ($reviewId === null || $adminReply === null) {
            Yii::error("Invalid parameters: reviewId or adminReply is missing.", __METHOD__);
            return ['success' => false, 'message' => 'Отсутствуют необходимые параметры.'];
        }

        $review = Review::findOne($reviewId);
        if ($review === null) {
            Yii::error("Review not found for reviewId: $reviewId", __METHOD__);
            return ['success' => false, 'message' => 'Отзыв не найден.'];
        }

        $review->admin_reply = $adminReply;

        if ($review->save()) {
            Yii::info("Reply saved successfully for reviewId: $reviewId", __METHOD__);
            return ['success' => true, 'message' => 'Ответ успешно сохранен.'];
        } else {
            Yii::error("Error saving reply for reviewId: $reviewId. Errors: " . json_encode($review->errors), __METHOD__);
            return ['success' => false, 'message' => 'Не удалось сохранить ответ.'];
        }
    }
    public function actionDeleteReview()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $review = Review::findOne($id);

        if ($review && $review->delete()) {
            return ['success' => true];
        } else {
            return ['success' => false];
        }
    }

    public function actionDeleteReply()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $reviewId = Yii::$app->request->post('reviewId');
        $review = Review::findOne($reviewId);

        if ($review) {
            $review->admin_reply = null;
            if ($review->save()) {
                return ['success' => true];
            }
        }

        return ['success' => false];
    }

    public function actionUpdate($id)
    {
        $model = User::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Данные успешно сохранены');
            return $this->refresh(); // или перенаправление на другую страницу
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

}
