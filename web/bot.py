import logging
from telegram import Update
from telegram.ext import Updater, CommandHandler, CallbackContext

# Вставьте ваш токен бота сюда
TOKEN = '7156288740:AAGVFtLRlwM_RKEbkHlnb35BeZU_p4xLdec'

# Настройка логгирования
logging.basicConfig(format='%(asctime)s - %(name)s - %(levelname)s - %(message)s', level=logging.INFO)

# Обработчик команды /start
def start(update: Update, context: CallbackContext) -> None:
    update.message.reply_text('Привет! Я бот пиццерии. Отправьте мне команду /promo для получения промокода на скидку к вашему следующему заказу.')

# Обработчик команды /promo
def promo(update: Update, context: CallbackContext) -> None:
    promo_code = generate_promo_code()  # Генерируем промокод (замените эту функцию на свою логику генерации)
    update.message.reply_text(f'Ваш уникальный промокод: {promo_code}. Введите его при оформлении заказа для получения скидки!')

# Функция для генерации уникального промокода
def generate_promo_code() -> str:
    # Здесь может быть ваша логика генерации промокода
    return 'ABC123'  # Пример промокода, замените на свою логику

def main() -> None:
    # Создаем экземпляр класса Updater и передаем ему токен бота
    updater = Updater(TOKEN)
    
    # Получаем диспетчер для регистрации обработчиков
    dispatcher = updater.dispatcher
    
    # Регистрируем обработчики команд
    dispatcher.add_handler(CommandHandler("start", start))
    dispatcher.add_handler(CommandHandler("promo", promo))
    
    # Запускаем бота
    updater.start_polling()
    
    # Бот будет работать, пока мы его не остановим
    updater.idle()

if __name__ == '__main__':
    main()
