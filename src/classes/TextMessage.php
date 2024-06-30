<?php

namespace vkbot\classes;

class TextMessage
{

    static public function getFirstInformationMessage()
    {
        $message = "Чтобы включить бота, отправьте текст:\nОпределитель Бот /старт\n\nДля того чтобы узнать другие команды:\nОпределитель Бот /помощь";
        return $message;
    }

    static public function getHelpMessage()
    {
        $message = "Список команд:\n/старт - включить бота\n/статистика - получить статистику по беседе\n/помощь - справочник по командам\n/clear - очистить ВСЮ ИСТОРИЮ";
        return $message;
    }


    static public function getTopWeekMessage()
    {
        $message = "📅 Топ неудачников этой недели!\n";
        return $message;
    }
    static public function getTopMonthMessage()
    {
        $message = "📅 Топ неудачников этого месяца!\n";
        return $message;
    }

    static public function getTopAllTimeMessage()
    {
        $message = "📅 Статистика неудачников за все время:\n";
        return $message;
    }

    static public function getEmptyHistoryTimeMessage()
    {
        $message = "♻  В статистике пока ничего еще нет\n";
        return $message;
    }


    static public function getNeedRightAdminMessage()
    {
        $message = "⛔ Необходимо дать права администратора беседы для бота";
        return $message;
    }

    static public function getLimitTimeMessage(string $limit)
    {
        $message = "";
        switch ($limit) {
            case "1":
                $message = "⛔ Лимит по времени еще не окончен, остался - $limit час";
                break;
            case "2":
                $message = "⛔ Лимит по времени еще не окончен, осталось - $limit часа";
                break;
            case "3":
                $message = "⛔ Лимит по времени еще не окончен, осталось - $limit часа";
                break;
            case "4":
                $message = "⛔ Лимит по времени еще не окончен, осталось - $limit часа";
                break;
            default:
                $message = "⛔ Лимит по времени еще не окончен, осталось - $limit часов";
        }
        return $message;
    }


    static public function getLoserMessage(string $firstNameLoser, string $lastNameLoser, string $sex)
    {
        $message = "";
        if ($firstNameLoser === "Саня" || $firstNameLoser === "Александр" || $firstNameLoser === "Саша" || $firstNameLoser === "Александра") {
            switch (rand(0, 2)) {
                case 0:
                    $message = "Психушка сообщает: сбежал опасный шизоид - $firstNameLoser $lastNameLoser";
                    break;
                case 1:
                    $message = "Внимание! Обнаружен шизоид - $firstNameLoser $lastNameLoser";
                    break;
                case 2:
                    $sex === "2" ? $message = $message = "Забыл выпить таблетки - $firstNameLoser $lastNameLoser" : $message = "Забыла выпить таблетки - $firstNameLoser $lastNameLoser";
                    break;
            }
        } else
            switch (rand(0, 7)) {
                case 0:
                    $sex === "2" ? $message = "$firstNameLoser $lastNameLoser был проклят" : $message = "$firstNameLoser $lastNameLoser была проклята";
                    break;
                case 1:
                    $message = "Неудачник дня - $firstNameLoser $lastNameLoser";
                    break;
                case 2:
                    $message = "Фортуна отвернулась от тебя $firstNameLoser $lastNameLoser - тебя ждут денежные неудачи...";
                    break;
                case 3:
                    $message = "Осторожно, $firstNameLoser $lastNameLoser, сегодня голуби могут нагадить на тебя!";
                    break;
                case 4:
                    $message = "Бурятские шаманы наслали порчу - животные тебя не любят $firstNameLoser $lastNameLoser";
                    break;
                case 5:
                    $sex === "2" ? $message = "$firstNameLoser $lastNameLoser пересек путь черной кошке - ты можешь заболеть" : $message = "$firstNameLoser $lastNameLoser пересекла путь черной кошке - ты можешь заболеть";
                    break;
                case 6:
                    $sex === "2" ? $message = "$firstNameLoser $lastNameLoser разбил зеркало - сегодня ты будешь ругаться с людьми" : $message = "$firstNameLoser $lastNameLoser разбила зеркало - сегодня ты будешь ругаться с людьми";
                    break;
                case 7:
                    $message = "Ретроградный Меркурий в Юпитере - $firstNameLoser $lastNameLoser хочет срать чаще...";
                    break;
            }
        return $message;
    }


    static public function getAllLosersMessage(string $firstNameLoser, string $lastNameLoser, string $sex)
    {
        $message = "";
        switch (rand(0, 3)) {
            case 0:
                $sex === "2" ? $message = "$firstNameLoser $lastNameLoser открыл ящик Пандоры - сегодня неудача преследует всех..." : $message = "$firstNameLoser $lastNameLoser открыла ящик Пандоры - сегодня неудача преследует всех...";
                break;
            case 1:
                $sex === "2" ?  $message = "$firstNameLoser $lastNameLoser продал свою душу Дьяволу, но Дьявола это не устроило - он забрал души и всех остальных" : $message = "$firstNameLoser $lastNameLoser продала свою душу Дьяволу, но Дьявола это не устроило - он забрал души и всех остальных";
                break;
            case 2:
                $sex === "2" ?  $message = "Аномальные метеоусловия! Отстойная погода преследует всех, где бы вы ни были" : $message = "$firstNameLoser $lastNameLoser решила поделиться неудачей со всеми остальными!";
                break;

        }
        return $message;
    }

    static public function getPositionTopMessage(string $firstName, string $lastName, string $position, string $countFailed)
    {
        $message = "";
        switch ($position) {
            case 0:
                $message = "🥇 $firstName $lastName - $countFailed\n";
                break;
            case 1:
                $message = "🥈 $firstName $lastName - $countFailed\n";
                break;
            case 2:
                $message = "🥉 $firstName $lastName - $countFailed\n";
                break;
            default:
                $message = "$firstName $lastName - $countFailed\n";
        }
        return $message;
    }


    static public function getClearMessage($isAdmin)
    {
        $message = "";
        switch ($isAdmin) {
            case "1":
                $message = "♻ История беседы очищена!\n";
                break;
            case "0":
                $message = "⛔ У вас нет прав администратора беседы чтобы очистить историю\n";
                break;
            case "-1":
                $message = "⚠ В данной беседе нет истории\n";
                break;
        }

        return $message;
    }
}
