# Правильная организация bitrix проекта
Вашему вниманию представлен достаточно удачный вариант организации битрикс проекта с использованием git и composer.
Используя данную структуру из проекта в проект Вы облегчите жизнь себе и другим разработчикам.

## Использование репозитория как шаблона в новом проекте
1. Клонируем или сохраняем репозиторий в любую дирректорию
2. Удаляем папку *.git* и файл *README.md*
3. Копируем содержимое дирректории в проект
4. Вносим нужные изменения в файл *composer.json* и выполняем `composer install` из папки *local*

## Использование репозитория как шаблона в существующем проекте
Здесь все как и в новом проекте, но нужно помнить о файле *init.php*, и если он существует, то дополнить его содержимое файлом из репозитория. Настоятельно рекомендую вынести все содержимое старого *init.php* в хелперы, это повысит читаемость кода.   
Если есть возможность, то желательно перенести все локальные модули и шаблоны в папку *local*, это упростит поддержку проекта и настройку git.  
В случае, когда перенести код в *local* нет возможности, то нужно исправить *.gitignore*, добавив в него код который вы хотите отслеживать. Примерно так:  
    
    /bitrix/*  
    !/bitrix/templates
    !/bitrix/php_interface
    /bitrix/php_interface/dbconn.php

Эти строки заставят git игнорировать файлы ядра и прочие файлы, которые не касаются шаблонов и папки *php_interface*.  

## Git, .gitignore
Файл *.gitignore* в этом репозитории лаконичен и понятен, но обычно требует небольшого дополнения в зависимости от проекта. Один из вариантов рассмотрен выше.  
**Не добавляйте код ядра в репозиторий!** Если репозиторий весит больше 50 мегабайт это повод задуматься о том, что там что-то лишнее.  

## Папка local
Хотя этому "нововведению" уже более 5 лет, многие разработчики продолжают упорно не замечать папку *local*. Хранение вашего кода в *local* упростит навигацию по коду, обеспечит порядок и облегчит работу с системами контроля версий.  
Подробно можно прочитать вот [здесь](https://dev.1c-bitrix.ru/community/blogs/vad/local-folder.php).

## Composer, автозагрузка классов, хелеперы
### Зачем мне composer в битриксе?
Как минимум для реализации адекватной автозагрузки классов. Даже если нет надобности в сторонних пакетах, всегда существуют классы-хелперы, обработчики событий и прочий код, который неплохо было бы организовать. Но это всего лишь небольшой бонус к мощному менеджеру пакетов с помощью которого, в пару действий, можно установить и поддерживать в актуальном состоянии любую библиотеку. 
При желании можно интегрировать в битрикс любой фреймворк. Например компонтенты symfony:
+ [Компонент шаблонизации из symfony в битрикс](https://github.com/mihaylichenko/bitrix-extension-view)

### Организация кода в проекте
+ *local/php_interface/project* - папка с классами касающимися только конкретно этого проекта
+ *local/php_interface/project/events* - классы обработчиков событий
+ *local/php_interface/project/helpers* - классы хелперов проекта
+ *local/php_interface/project/helpers/Def.php* - класс с константами проекта, очень полезно определять их в одном месте
+ *local/php_interface/project/helpers/TemplateHelper.php* - класс хелпера шаблона (шаблонов)  

Если есть код, который вы перемещаете из проекта в проект, то его лучше вынести в отдельную папку c именем вендора внутри *php_interface*. Здесь для примера это папка *wn*.  

### composer.json и автозагрузка
Для того, чтобы composer знал о ваших классах, нужно прописать пути к ним и их пространства имен в *composer.json*.  
    
    "autoload": {
        "psr-4": {
            "Project\\Helpers\\": "php_interface/project/helpers",
            "Project\\Events\\": "php_interface/project/events",
            "Wn\\Helpers\\": "php_interface/wn/helpers"
        }
    }
После этого требуется обновить загрузчик коммандой `composer dump-autoload --optimize`.
Для того, чтобы автозагрузчик работал, нужно включить файл *autoload.php* в наш init.php:  

    if(!file_exists(__DIR__.'/../vendor/autoload.php')){
        throw new \Bitrix\Main\Config\ConfigurationException('Use "composer install" in the /local folder');
    }
    require(__DIR__.'/../vendor/autoload.php');
    
Код выше сгенерирует исключение, если вы забыли запустить `composer install`.

## Миграции
Если вы пришли в битрикс из мира фреймворков типа yii или symfony то поддержка баз данных всех копий проекта в актуальном состоянии может повергнуть вас в шок. Но к счастью есть несколько проектов которые частично решают эту проблему.  
Я предлагаю использовать [этот](https://github.com/arrilot/bitrix-migrations) вариант. Он использует модули symfony, понятен, расширяем и прост. В наш *composer.json* он уже включен:

    "require": {
        "arrilot/bitrix-migrations": "^2.3"
    }
