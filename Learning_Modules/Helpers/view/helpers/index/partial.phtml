<?php
/*
 * Хелпер partial() подключает шаблон в вид и передаёт этому шаблону массив параметров во втором аргументе хелпера.
 * Подключаемый шаблон должен быть зарегистрирован в конфиге модуля:
 * 'template_map' => array(
 *      'layout/PartialTemplate' => __DIR__ . '/../view/layout/partial-template.phtml',
 *	),
 */

$array = ['from' => 'Team Framework', 'subject' => 'view partials array'];
$object = (object)['from' => 'Team Framework', 'subject' => 'view partials object'];

// вывести шаблон 'layout/PartialTemplate' с параметрами в виде массива array(...)
echo $this->partial('layout/PartialTemplate', $array);
  

// передаём имя ключа, с которым будет сохранён передаваемый во втором аргументе объект в $this->partial() 
$this->partial()->setObjectKey('keyObject');
echo $this->partial('layout/PartialTemplate', $object);

// это то же самое что и:
//echo $this->partial('layout/PartialTemplate', ['keyObject'=> $object] );

/* 
 * Подключаемый шаблон (находится в view/layout/partial-template.phtml)
 * для массива во втором параметре
 * <ul>
 *      <li>From: <?php echo $this->escapeHtml($this->from) ?></li>
 *      <li>Subject: <?php echo $this->escapeHtml($this->subject) ?></li>
 * </ul>
 * для объекта во втором параметре
 * <ul>
 *      <li>From: <?php echo $this->escapeHtml($this->keyObject->from) ?></li>
 *      <li>Subject: <?php echo $this->escapeHtml($this->keyObject->subject) ?></li>
 * </ul> 
 */ 

/*
 * Хелпер partialLoop()
 * Если необходимо многократно вызывать шаблон в цикле с передачей каждый раз новых данных,
 * то надо использовать хелпер partialLoop(), а не простой partial() (он снижает производительность 
 * потому что он будет вызываться каждый раз для каждой итерации):
 * Вторым параметром partialLoop() должен быть массив или объект implementing Traversable (итерируемый)
 */
 
$model = array(
    array('key' => 'Mammal', 'value' => 'Camel'),
    array('key' => 'Bird', 'value' => 'Penguin'),
    array('key' => 'Reptile', 'value' => 'Asp'),
    array('key' => 'Fish', 'value' => 'Flounder'),
);
$arrayIterator = new \ArrayIterator($model);

echo "<dl>\n";
// передаём массив
echo $this->partialLoop('layout/PartialloopTemplate', $model);
// передаём итератор
//echo $this->partialLoop('layout/PartialloopTemplate', $arrayIterator);
echo "</dl>\n";

/*
 * Подключаемый шаблон 'layout/PartialloopTemplate' (partialloop-template.phtml):
 *      <dt><?php echo $this->key ?></dt>
 *      <dd><?php echo $this->value ?></dd> 
 */
/* Получим: 
        <dl>
            <dt>Mammal</dt>
            <dd>Camel</dd>

            <dt>Bird</dt>
            <dd>Penguin</dd>

            <dt>Reptile</dt>
            <dd>Asp</dd>

            <dt>Fish</dt>
            <dd>Flounder</dd>
        </dl>
 */