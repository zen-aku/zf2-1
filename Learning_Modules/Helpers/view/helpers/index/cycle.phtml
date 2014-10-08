<?php
/*
 * Хелпер cycle() реализует /Spl/Iterator как бесконечный итератор. 
 * Позволяет проводить перебор элементов двумерного массива в прямом и обратном порядке.
 * Может хранить несколько разных массивов, которые может итерировать по запросу.
 * __invoke(array $data = array(), $name = self::DEFAULT_NAME)
 *		$data - пользовательский массив
 *		$name - ключ, под которым массив $data будет храниться в $this->data[$name] = $data. По умолчанию $name = 'default'
 * next() - возвращает объект Сycle с переведённым ключом в следующую позицию. 
 *		По достижению последней позиции переводится на первую и так по кругу бесконечно.
 * prev() - возвращает объект Сycle с переведённым ключом в предыдущую позицию.
 *		По достижению первой позиции переводится на последнюю и так по кругу бесконечно.
 * rewind() - перевод итератора в начальную позицию
 * assign($data = array(), $name = self::DEFAULT_NAME) - задать массив, ключ массива и перевести его в начальную позицию
 *		Это __invoke() вместе с rewind()
 * 
 * Хелпер cycle() используется внутри других циклов с конечным числом итераций 
 * или явно задают количество итераций массива cycle(), чтобы избежать бесконечного цикла
 */
$books = ['идиот', 'война и мир', 'гарри поттер', 'тихий дон'];
$colors = ['#F0F0F0', '#FFF'];
?>

<!-- Передаём в итератор массив под именем 'default' и итерируем его в прямом порядке пока итерируется массив $books-->
<?php $this->cycle($colors); 
// если дефолтный массив уже итерировался ранее, то для перевода его в  начальное положение надо:
// $cycleColors->rewind; // поэтому лучше передавать массив в хелпер с помощью метода assign()-ниже
?>
<table>
    <?php foreach ($books as $book): ?>	
        <tr style="background-color:<?php echo $this->cycle()->next();?>">
            <td><?php echo $book; ?></td>
        </tr>
    <?php endforeach ?>
</table>
<br />


<!-- Передаём в итератор массив под именем 'default', переводим его в начальное положение и итерируем его в обратном порядке пока итерируется массив $books-->
<?php $this->cycle()->assign($colors) ?>
<table>
    <?php foreach ($books as $book): ?>
		<tr style="background-color: <?php echo $this->cycle()->prev(); ?>">
		   <td><?php echo $book; ?></td>
		</tr>
    <?php endforeach ?>
</table>
<br />


<!-- Передаём в итератор в ключ 'default' массив $colors, а в ключ 'number' числовой массив  -->
<?php $this->cycle()->rewind();?>
<table>
    <?php foreach ($books as $book): ?>
        <tr style="background-color: <?php echo $this->cycle($colors)->next(); ?>">
            <td><?php echo $this->cycle(array(1, 2), 'number')->next(); ?></td>
            <td><?php echo $book ?></td>
        </tr>
    <?php endforeach ?>
</table>
<br />


<!-- Передаём в итератор в ключ 'colors' массив $colors, а в ключ 'number' числовой массив и переводим автоматически массивы вначальное состояние 
	 Итерируем массивы, вызывая их по имени ключа с помощью setName($name)-->
<?php
$this->cycle()->assign($colors, 'colors');
$this->cycle()->assign([1, 2, 3], 'numbers');
?>
<table>
    <?php foreach ($books as $book): ?>
        <tr style="background-color: <?php echo $this->cycle()->setName('colors')->next(); ?>">
            <td><?php echo $this->cycle()->setName('numbers')->prev(); ?></td>
            <td><?php echo $book; ?></td>
        </tr>
    <?php endforeach ?>
</table>

