/**
 * Абстрактный класс балуна для карт Яндекса
 */
export default abstract class AbstractBalloon {
    /**
     * Карта на которой, будет отрисовываться балун
     */
    map: object|null = null
    /**
     * Функция для отрисовки балуна
     * @param data
     */
    abstract render(data: object): string;

    /**
     * Функция для привязки прослушивателей событий
     */
    abstract bindListener(): void;

    /**
     * Функция для отвязки прослушивателей событий
     */
    abstract unbindListener(): void;
}