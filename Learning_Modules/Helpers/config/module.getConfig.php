<?php
return array(

	/*
	 * View
	 */
	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
		'template_map' => array(
			'layout/EventsLayout' => __DIR__ . '/../view/layout/events-layout.phtml',
            'layout/PartialTemplate' => __DIR__ . '/../view/layout/partial-template.phtml',
            'layout/PartialloopTemplate' => __DIR__ . '/../view/layout/partialloop-template.phtml',
            'layout/RenderToPlaceholderTemplate' => __DIR__ . '/../view/layout/render-to-placeholder-template.phtml',
            'layout/ArticleTemplate' => __DIR__ . '/../view/layout/article-template.phtml',
            'layout/SidebarTemplate' => __DIR__ . '/../view/layout/sidebar-template.phtml',
		),
	),
    
    /*
     * Конфиги хелперов
     */
    'view_helper_config' => array(
        // Конфигурация хелпера flashmessenger() (настройки html-шаблона сообщения)
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        )
    ),

);
