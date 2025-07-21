<h1>Установка и запуск Laravel-проекта с использованием XAMPP</h1>

<ol>
  <li>
    <strong>Убедитесь, что XAMPP запущен:</strong><br>
    Запустите <code>Apache</code> и <code>MySQL</code> в панели управления XAMPP.
  </li>

  <li>
    <strong>Клонируйте репозиторий в папку XAMPP: C:/xampp/htdocs</strong><br>
    <code>git clone https://github.com/muver1337/php-test-project.git</code>
  </li>

  <li>
    <strong>С помощью вашей IDE перейдите в директорию проекта: php-test-project</strong><br>
  </li>

  <li>
    <strong>Установите зависимости Composer:</strong><br>
    <code>composer install</code>
  </li>

  <li>
    <strong>Создайте <code>.env</code> файл:</strong><br>
    <code>cp .env.example .env</code>
  </li>

  <li>
    <strong>Сгенерируйте ключ приложения:</strong><br>
    <code>php artisan key:generate</code>
  </li>

  <li>
    <strong>Создайте базу данных в phpMyAdmin:</strong><br>
    Откройте <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a> и создайте новую БД (например, <code>laravel_app</code>).
  </li>

  <li>
    <strong>Настройте подключение к БД в файле <code>.env</code>:</strong><br>
    <pre>
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=root
DB_PASSWORD=
    </pre>
  </li>

  <li>
    <strong>Выполните миграции:</strong><br>
    <code>php artisan migrate</code>
  </li>

  <li>
    <strong>Запустите сидеры для наполнения БД:</strong><br>
    <code>php artisan db:seed</code>
  </li>

  <li>
    Затем проверьте работу серверной части в Postman.
  </li>

  <li>
      <a href="https://interstellar-eclipse-410947.postman.co/workspace/My-Workspace~3e827ff7-4ac4-4df0-8e4f-50fa4a56a9cc/collection/26700924-63588a7e-0246-4d6e-89e7-7b6b8899656d?action=share&creator=26700924"> Ссылка на коллекцию Postman
  </li>
</ol>
