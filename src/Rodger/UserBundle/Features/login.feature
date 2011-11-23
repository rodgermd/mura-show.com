# language: ru
Функционал: Логин
  Чтобы видеть список альбомов
  Как посетитель сайта
  Я должен иметь возможность видеть список альбомов

  Предыстория:
    Допустим на сайте зарегистрированы:
      | user    | email              | password | roles            |
      | everzet | ever.zet@gmail.com | qwerty   | ROLE_SUPER_ADMIN |
      | pilot   | pilot@gmail.com    | god      | ROLE_USER        |
      | igor    | igor@gmail.com     | 1234     | ROLE_USER        |

  Сценарий: Стартовая страница
    Если я на странице "http://www.mura-show.local/"
    То я должен видеть "murashow.com"
    И я должен видеть "login"

  Сценарий: страница логин
    Если я на странице "http://www.mura-show.local/"
    И я кликаю по ссылке "login"
    То я должен видеть "Login form:"