<?php
/*
 * Хелперы escape...() используют соответствующие методы класса модуля Zend\Escaper\Escaper.php:
 *      escapeHtml($string) -  Escaper::escapeHtml($string),
 *      escapeHtmlAttr($string) - Escaper::escapeHtmlAttr($string)
 *      escapeJs($string) - Escaper::escapeJs($string)
 *      escapeUrl($string) - Escaper::escapeUrl($string)
 *      escapeCss($string) - Escaper::escapeCss($string)
 * Экранирование для предотвращения XSS-атак с помощью escape-методов производится над соответствующими строковыми данными,
 * которые передаются в браузер (в вид). Escape надо использовать только для вида (view)!!!
 * Входные данные надо экранировать другими способами, напр. Zend\Filter-компонентами, HTMLPurifier or PHP’s Filter компонентами
 *
 * Экранирование с помощью escape широко используется в других местах фреймворка и поэтому, 
 * прежде чем применить хелперы escape надо посмотреть, а не использовалось ли уже экранирование
 * escape в применяемых сервисах фреймворка. Напр. хелперы head...() и html...() уже используют
 * экранирование и потому дополнительного экранирвоания не требуют.
 */
 
// Прямое использование класса Zend\Escaper\Escaper
$escaper = new Zend\Escaper\Escaper('utf-8');

// &lt;script&gt;alert(&quot;zf2&quot;)&lt;/script&gt;
echo $escaper->escapeHtml('<script>alert("zf2")</script>')."<br />\n";

// &lt;script&gt;alert&#x28;&quot;zf2&quot;&#x29;&lt;&#x2F;script&gt;
echo $escaper->escapeHtmlAttr('<script>alert("zf2")</script>')."<br />\n";

// \x3Cscript\x3Ealert\x28\x22zf2\x22\x29\x3C\x2Fscript\x3E
echo $escaper->escapeJs('<script>alert("zf2")</script>')."<br />\n";

// \3C script\3E alert\28 \22 zf2\22 \29 \3C \2F script\3E
echo $escaper->escapeCss('<script>alert("zf2")</script>')."<br />\n";

// %3Cscript%3Ealert%28%22zf2%22%29%3C%2Fscript%3E
echo $escaper->escapeUrl('<script>alert("zf2")</script>')."<br />\n";
echo "<hr>\n";


// Используем хелперы вместо прямого вызова методов Escaper
/*
 ********** escapeHtml() - экранирует спец символы в html-контексте
 */
// Выводить такой код где-то в html-шаблоне будет небезопасно
$input = '<script>alert("Safe output!")</script>'; 
// экранированный код будет: &lt;script&gt;alert(&quot;Safe output!&quot;)&lt;/script&gt;
?>
<div><?php echo $this->escapeHtml($input); ?> </div> 


<?php
/*
 *********** escapeHtmlAttr() - экранирует спец символы в контексте аттрибутов html-элементов
 */
// Передавать значение аттрибута html-элементу в таком виде будет небезовасно
$input = "faketitle onmouseover=alert(/ZF2!/);"; 
// экранированный код будет: <span title=faketitle&#x20;onmouseover&#x3D;alert&#x28;&#x2F;ZF2&#x21;&#x2F;&#x29;&#x3B;>
?>
<div>
    <span title=<?php echo $this->escapeHtmlAttr($input); ?>>
        What framework are you using?
    </span>
</div>


<?php
/*
 ************ escapeJs() - экранирует спец символы в контексте js-кода
 */
// Передавать код js в html в таком виде будет небезопасно
$input = "bar&quot;; alert(&quot;Meow!&quot;); var xss=&quot;true";
//  экранированный код будет: var foo = bar\x26quot\x3B\x3B\x20alert\x28\x26quot\x3BMeow\x21\x26quot\x3B\x29\x3B\x20var\x20xss\x3D\x26quot\x3Btrue;
?>
<script type="text/javascript">
    var foo = <?php echo $this->escapeJs($input); ?>;
</script>


<?php
/*
 ************ escapeCss() - экранирует спец символы в контексте Css-кода
 */
// передавать Css-код в таком виде будет небезопасно
$input ="
    body {
        background-image: url('http://example.com/foo.jpg?</style><script>alert(1)</script>');
    }";
// экранированный код будет: body\20 \7B \A \20 \20 \20 \20 background\2D image\3A \20 url\28 ...
?>
<style>
    <?php echo $this->escapeCss($input); ?>
</style>


<?php
/*
 ************ escapeUrl() - экранирует спец символы в контексте Url 
 */
// передавать Url в таком виде будет небезопасно
$input = <<<INPUT
    " onmouseover="alert('zf2')
INPUT;
// экранированный код будет: %20%20%20%20%22%20onmouseover%3D%22alert%28%27zf2%27%29
?>
<a href="http://example.com/?name=<?php echo $this->escapeUrl($input); ?>">Click here!</a>
