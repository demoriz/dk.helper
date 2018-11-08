# dk.helper
Модуль для [1С-Битрикс](http://www.1c-bitrix.ru/). Он не несёт в себе ничего нового, кроме "синтаксического сахара" используемого автором [API](http://dev.1c-bitrix.ru/api_help/) cms [1С-Битрикс](http://www.1c-bitrix.ru/).

Распространяется под лицензией [MIT](https://en.wikipedia.org/wiki/MIT_License). Автор не принимает на себя никаких гарантийных обязательств в отношении данного модуля и не несет ответственности за:

  * любой прямой или косвенный ущерб и упущенную выгоду, даже если это стало результатом использования или невозможности использования модуля;
  * убытки, включая общие, предвидимые, реальные, прямые, косвенные и прочие убытки, включая утрату или искажение информации, убытки, понесенные Пользователем или третьими лицами, невозможность работы модуля и несовместимость с любым другим модулем и т.д.
  * за любые повреждения оборудования или программного обеспечения Пользователя, возникшие в результате использовании модуля.

-----------------------------------

**Краткий перечень классов и методов:**

## DK\Helper\Highload\Element
```php
Element::getElement($strIblockData, $arFilter = array(), $arSelect = array(), $intLimit = 0)
```
_Возвращает элементы Highload инфоблока в виде массива. При необходимости можно использовать фильтрацию, указать нужные поля и ограничить количество. В $strIblockData передаётся либо id Highload инфоблока либо TABLE_NAME._
```php
Element::update($strIblockData, $intElementID, $arUpdate)
```
_Обновляет элемент Highload инфоблока. Принимает на вход id инфоблока, id элемента и массив полей со значениями. Можно передавать только то поле которое необходимо обновить. Возвращает true или вызывает исключение с текстом ошибки. В $strIblockData передаётся либо id Highload инфоблока либо TABLE_NAME._
```php
Element::add($strIblockData, $arFields)
```
_Добавляет новый элемент Highload инфоблока. Принимает на вход id инфоблока и массив полей со значениями. Возвращает id нового элемента или вызывает исключение с текстом ошибки. В $strIblockData передаётся либо id Highload инфоблока либо TABLE_NAME._
```php
Element::getAllFieldsByReference($arReference)
```
_Принимает на вход массив полей свойства типа "справочник", получаемый в компанентах "bitrix:catalog.section", "bitrix:catalog.element" и подобных в "$arResult\['DISPLAY_PROPERTIES'\]\[#property code#\]" либо "$arResult\['ITEMS'\]\[#item number#\]\['DISPLAY_PROPERTIES'\]\[#property code#\]". Возвращает принимаемый массив, дополненный полями соответствующего элемента Highload инфоблока._
```php
Element::delete($strIblockData, $arFields)
```
_Удаляет элемент Highload инфоблока. Принимает на вход id инфоблока и id элемента. В $strIblockData передаётся либо id Highload инфоблока либо TABLE_NAME._

## DK\Helper\Iblock\Element
```php
Element::delete($arIDs)
```
_Пакетное удаление элементов информационных блоков._
```php
Element::getFieldsByID($intElementID, $strFieldName = '', $bRefresh = false)
```
_Возвращает конкретное поле или массив полей элемента информационного блока. Кэширует выбранные данные в пределах одного хита. Если передать $bRefresh как true, то выполнит запрос к БД и обновит кэш._
```php
Element::getPropertiesByID($intElementID, $strPropertyName = '', $bRefresh = false)
```
_Возвращает конкретное свойство или массив свойств элемента информационного блока. Кэширует выбранные данные в пределах одного хита. Если передать $bRefresh как true, то выполнит запрос к БД и обновит кэш._
```php
Element::deactivate($arIDs)
```
_Деактивирует элементы инфоблока id которых переданы в массиве. В случае успеха возвращает true, или false в лучае неудачи._
```php
Element::activate($arIDs)
```
_Активирует элементы инфоблока id которых переданы в массиве. В случае успеха возвращает true, или false в лучае неудачи._

## DK\Helper\Iblock\Section
```php
Section::delete($arIDs)
```
_Пакетное удаление разделов информационных блоков._
```php
Section::getFieldsByID($intSectionID, $strFieldName = '')
```
_Возвращает конкретное поле или массив полей раздела информационного блока._

## DK\Helper\Iblock\Property
```php
Property::getIdByName($intIblockID, $strName, $arParams = array())
```
_Возвращает ID свойства инфоблока по его имени. Если совойство с таким именем не найдено то оно будет создано. В массиве $arParams можно передать поля для нового свойства._
```php
Property::getIdEnumValue($intIblockID, $strPropertyName, $strValueName)
```
_Возвращает ID свойства-списка инфоблока по его имени и ID варианта значения так же по его имени в виде массива. Если совойство с таким именем не найдено то оно будет создано. Если искомый вариант значения не найден, то он будет создан._
```php
Property::getValuesPropertyString($intIblockID, $strPropertyCode)
```
_Возвращает массив вариантов строкового свойства с кодом $strPropertyCode всех элементов ифоблока с id $intIblockID._
```php
Property::getValuesPropertyEnum($intIblockID, $strPropertyCode)
```
_Возвращает массив вариантов спискового свойства с кодом $strPropertyCode ифоблока с id $intIblockID._

## DK\Helper\IO\Serialize
```php
Serialize::write($strName, $obData)
```
_Сохраняет любой объект в виде файла в upload_
```php
Serialize::ride($strName)
```
_Получает любой сохранённый предыдущим методом объект из файла в upload._

## DK\Helper\Sale\Basket
```php
Basket::clean($obBasket)
```
_Очищает корзину. В качестве параметра принимает объект корзины Bitrix\Sale\Basket. Не возвращает ничего._
```php
Basket::add($intProductID, $intQuantity = 1, $isXmlId = false)
```
_Добавляет товар с кодом $intProductID и количеством $intQuantity в корзину. Если бы передан $isXmlId как true, будут добавлены XML_ID поля товара и раздела. Не возвращает ничего._
```php
Basket::update($intProductID, $intQuantity = 1, $isXmlId = false)
```
_Обновляет информацию о товаре в корзине._
```php
Basket::delete($intItemID)
```
_Удаляет позицию из корзины. Принимает ID позиции корзины._
```php
Basket::delete($intProductID)
```
_Удаляет позицию из корзины Принимает ID товара._
```php
Basket::count($isOnlyItems = false)
```
_Возвращает количество товаров в корзине, для данного сайта и пользователя. Если в качестве параметра передано true, то подсчитываться будет только количество позиций, без учёта их количества._
```php
Basket::getDiscountSum($strCoupon = '')
```
_Рассчитывает скидку для корзины текущего пользователя, включая правила работы с корзиной. Если передан купон, то перед рассчётом он будет активирован. Возвращает массив с общей скидочной ценой корзины и по каждому товару отдельно._

## DK\Helper\Sale\Order
```php
Order::getPropertyValueByCode($obOrder, $strCode)
```
_Возвращает значение свойства заказа. На вход принимает объект заказа ([d7](http://dev.1c-bitrix.ru/api_d7/bitrix/sale/order/index.php)) и символьный код свойства._
```php
Order::setPropertyValueByCode($obOrder, $strCode, $strValue)
```
_Записывает значение свойства заказа. На вход принимает объект заказа ([d7](http://dev.1c-bitrix.ru/api_d7/bitrix/sale/order/index.php)), символьный код свойства и значение._
```php
Order::getDeliveries($intUserID = NULL)
```
_Возвращает массив объектов доставок с учётом ограничений. Если id пользователя не будет передан то произойдёт попытка получить его из глобального объекта, в случае неудачи будет создан аноним._
```php
Order::getPaySystems($intUserID = NULL, $intDeliveryID = false)
```
_Возвращает массив платёжных систем с учётом ограничений. Если id пользователя не будет передан то произойдёт попытка получить его из глобального объекта, в случае неудачи будет создан аноним. Необходимо передать id службы доставки если используется ограничение по доставке._
```php
Order::simpleOrder($intUserID = NULL, $arProperties)
```
_Создаёт простой заказ. Если id пользователя не будет передан то произойдёт попытка получить его из глобального объекта, в случае неудачи будет создан аноним. Массив $arProperties обязательно должен имет заполненными ключи 'DELIVERY_ID', 'PAYMENT_ID'. Из не обязательных 'PERSONAL_ID', 'COUPON'. Так же можно передать в ключе 'ORDER_PROPERTIES' масив со свойствами заказа, а в ключе 'ORDER_FIELDS' массив с полями заказа. Массив должен иметь формат 'CODE' => 'VALUE'. Метод создавался для часто используемого функционала "Заказ в один клик". Для более сложной задачи создания заказа, он вряд ли подойдёт._
```php
Order::byOneClick($intUserID = NULL, $arProperties)
```
_Создаёт быстрый заказ. От предыдущего метода отличается необходимостью передать в ключе 'PRODUCT_ID' в массиве $arProperties идентификатор товара. Метод создавался для часто используемого функционала "Заказ в один клик"._

## DK\Helper\Sale\Price
```php
Price::setMinMaxByOffer($intOfferElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
```
_Заполняет указанные свойства товара, соответствующего переданному торговому предложению, минимальной и максимальной ценой из всех имеющихся предложений данного товара._
```php
Price::setMinMaxByProduct($intProductElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
```
_Заполняет указанные свойства переданного товара минимальной и максимальной ценой из всех имеющихся предложений данного товара._
```php
Price::add($intProductID, $intPriceTypeID, $floatPrice, $strCurrency = 'RUB')
```
_Устанавливает для товара $intProductID цену типа $intPriceTypeID в значение $floatPrice. Если цена есть то обновлет. Если нет - добавляет._

## DK\Helper\Others\Cookie
```php
Cookie::getCookie($strName = false)
```
_Получает конктретно указанный cookie или массив всех имеющихся._
```php
Cookie::setCookie($strName, $strValue, $strDomain = '')
```
_Записывает данные в cookie._

## DK\Helper\Others\Strings
```php
Strings::getStringOfNum($intNum)
```
_Возвращает прописью переданное число._
```php
Strings::declension($intCount, $arVariants, $bIncludeNumber = false)
```
_Склоняет и возвращает существительное согласно переданному числительному._

## DK\Helper\Others\DateTime
```php
DateTime::getInterval($obFirstData, $obSecondData)
```
_Принимает два объекта Bitrix\Main\Type\DateTime и возвращает массив с разницей. Ключи возвращаемого массива: 0 - секунды; 1 - минуты; 2 - часы; 3 - дни; 4 - года._
```php
DateTime::reFormatDate($strDate, $strFormat1, $strFormat2)
```
_Принимает 3 строки. Дату, исходный формат, конечный формат. Возвращает строку с датой в конечном формате._

## DK\Helper\Others\Image
```php
Image::resize($intFileID, $intWidth = 999999, $intHeight = 999999)
```
_Уменьшает изображение с id $intFileID до ширины $intWidth и высоты $intHeight пропорционально. Возвращает массив._

## DK\Helper\Main\User
```php
User::getID($isAllowAnonymous = false)
```
_Возвращает id текущего пользователя или NULL, если передан параметр $isAllowAnonymous как true, то в случае отутсвия пользователя создаст анонима и вернёт его id._
```php
User::getByLogin($strLogin, $arParams = array())
```
_Возвращает массив с параметрами пользователя соответствущими переданному логину или false. Если пользователь не найден и передан массив $arParams то создаётся новые из полей этого массива._

## DK\Helper\Main\Debug
```php
Debug::show($obData, $strFlag = false)
```
_Выводит в месте вызова содержимое переданного объёкта. Если $strFlag отличен от false отладочная информация будет видна всем, в противном случае только аккаунтам с административной привилегией. Метод является более удобочитаемой заменой стандартных функций print_r() и var_dump()._

## DK\Helper\Main\GarbageStorage
```php
GarbageStorage::set($strName, $mixValue)
```
_Глобальное хранилище данных. Помойка. Метод сохраняет произвольные данные. Работает в пределах одного хита._
```php
GarbageStorage::get($strName)
```
_Глобальное хранилище данных. Помойка. Метод возвращает произвольные данные. Работает в пределах одного хита._

## DK\Helper\Main\ClientStorage
```php
ClientStorage::set($strName, $mixValue)
```
_Персональное хранилище клиента. Включает в себя сессию и куки. Метод сохраняет произвольные данные._
```php
ClientStorage::get($strName)
```
_Персональное хранилище клиента. Включает в себя сессию и куки. Метод возвращает произвольные данные._