<?php

class IndexController extends AbstractActionController {
    function indexAction() {
		
		/*
		 * Zend\Mvc\Controller\Plugin\Layout::__invoke($template = null)
		 * Позволяет изменить шаблон вида для данного экшена.
		 * Новый шаблон должен быть прописан в конфиге в 'template_map'
		 * Если новый шаблон не задан, то вызывается шаблон по стандартному пути для данного экшена
		 */
		$this->layout('layout/NewLayout');
		
		
		/*
		 * Zend\Mvc\Controller\Plugin\Layout::__invoke()::setTemplate($template)
		 * Аналог предыдущего задания шаблона, но с обязательным аргументом $template
		 */
		$this->layout()->setTemplate('layout/NewLayout');
        
        return ViewModel(
            array(
                'greeting' => $this->greetingService->getGreeting(),
                'date' => $this->currentDate(),
            )
        );
        
        /////////////////////////////
        /*
         * Mакет(layout) – это не более, чем модель представления, которая ссылается
         * на другую модель представления, сгенерированную другим действием контроллера или
         * другим контроллером в качестве "дочерней" (см. Doc.Forward.php showAction()). 
         * Чтобы получить доступ к модели представления в шаблоне макета,
         * фреймворк автоматически регистрирует ключ content, точно так, как мы создали вручную ключ widgetContent в Doc.Forward.php showAction(). 
         * Таким образом, в шаблоне макета результат(layout) действия контроллера можно получить следующим образом:
         */
        /*
            // Mакет(layout):newlayout.phtml
            <html>
            <head>
                <title>My website</title>
            </head>
            <body>
                // вызвать модель представления родителя (основной вид ViewModel:index.phtml экшена indexAction() контроллера)
                <?php echo $this->content; ?>
            </body>
            </html>
        */
        /*
         * Задача макета (layout) проста – вывести doctype, css и js файлы, общие для всех видов-шаблонов контроллеров. 
         * Другими словами, макет служит своеобразной "оберткой" для видов-шаблонов контроллеров. 
         * Для упрощения понимания, можно назвать макет (layout) "файлом темы". 
         * Кстати, Zend Framework 2 в стандартной комплектации в качестве темы (css и js) содержит "twitter bootstrap" (http://twitter.github.io/bootstrap).    
         */
	}
	
	function ViewAction() {
		
		/*... какой-то код */
		
		$view = new View\Model();
 
		/*
		 * Вызываем из карты шаблонов готовые шаблоны 
		 */
		
        $articleView = new ViewModel(array('article' => $article));
        $articleView->setTemplate('content/article');
 
        $primarySidebarView = new ViewModel();
        $primarySidebarView->setTemplate('content/main-sidebar');
 
        $secondarySidebarView = new ViewModel();
        $secondarySidebarView->setTemplate('content/secondary-sidebar');
 
        $sidebarBlockView = new ViewModel();
        $sidebarBlockView->setTemplate('content/block');
 
		/*
		 * Создаём шаблон вызовом другого экшена
		 * см. плагин forward() (Doc.Forward.php)
		 */
		// модель представления экшена Helloworld\Controller\Widget::indexAction() кэшируется в переменной $widget.
        $widget = $this->forward()->dispatch('Helloworld\Controller\Widget');
		
		/*
		 * Передаём шаблоны в возвращаемый вид $view
		 */
        $secondarySidebarView->addChild($sidebarBlockView, 'block'); 
        $view->addChild($articleView, 'article')
             ->addChild($primarySidebarView, 'sidebar_primary')
             ->addChild($secondarySidebarView, 'sidebar_secondary')
			 ->addChild($widget, 'widget');
 
        return $view;
	
		/*
		 * Теперь в виде экшена можно в разных местах вызывать переданные шаблоны:
		 *
			<!-- This is from the $articleView view model, and the "content/article" template -->
			<article class="twelve columns">
				<?php echo $this->article ?>
			</article>

			<!-- This is from the $primarySidebarView view model, and the "content/main-sidebar template -->
			<div class="two columns sidebar">
				<?php echo $this->sidebar_primary ?>
			</div>

			<!-- This is from the $secondarySidebarView view model, and the "content/secondary-sidebar template -->
			<div class="two columns sidebar">
				<?php echo $this->sidebar_secondary ?>
			</div>

			<!-- This is from the $sidebarBlockView view model, and the "content/block template -->
			<div class="block">
				<?php echo $this->block ?>
			</div>
		
			<div class="widget">
				<?php echo $this->widget ?>
			</div>	 
		*/
		
	}
	
}