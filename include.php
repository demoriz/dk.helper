<?php
CModule::AddAutoloadClasses(
    'dk.helper',
    array(
        'DK\Helper\Highload\Element' => 'lib/highload/element.php',
        'DK\Helper\IO\Serialize' => 'lib/io/serialize.php',
        'DK\Helper\IO\File' => 'lib/io/file.php',
        'DK\Helper\Iblock\Element' => 'lib/iblock/element.php',
        'DK\Helper\Iblock\Section' => 'lib/iblock/section.php',
        'DK\Helper\Iblock\Property' => 'lib/iblock/property.php',
        'DK\Helper\Sale\Order' => 'lib/sale/order.php',
        'DK\Helper\Sale\Price' => 'lib/sale/price.php',
        'DK\Helper\Catalog\Price' => 'lib/catalog/price.php',
        'DK\Helper\Sale\Basket' => 'lib/sale/basket.php',
        'DK\Helper\Catalog\Basket' => 'lib/catalog/basket.php',
        'DK\Helper\Others\Strings' => 'lib/others/string.php',
        'DK\Helper\Others\Cookie' => 'lib/others/cookie.php',
        'DK\Helper\Others\DateTime' => 'lib/others/datetime.php',
        'DK\Helper\Others\Image' => 'lib/others/image.php',
        'DK\Helper\Others\Struct' => 'lib/others/struct.php',
        'DK\Helper\Main\User' => 'lib/main/user.php',
        'DK\Helper\Main\Debug' => 'lib/main/debug.php',
        'DK\Helper\Main\GarbageStorage' => 'lib/main/garbage_storage.php',
        'DK\Helper\Main\ClientStorage' => 'lib/main/client_storage.php',
        'DK\Helper\Main\System' => 'lib/main/system.php'
    )
);