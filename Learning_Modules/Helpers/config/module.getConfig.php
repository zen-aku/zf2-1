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
