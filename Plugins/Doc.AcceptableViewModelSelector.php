<?php

class IndexController extends AbstractActionController {
    
    function indexAction() {
        /*
         * Zend\Mvc\Controller\Plugin\AcceptableViewModelSelector::__invoke(array $matchAgainst = null, $returnDefault = true, & $resultReference = null)
         * Выбирает представление из Zend\View\Model\ в зависимости от переданного соответствия из
         * запроса из Header[Accept] (напр. [Accept] => text/html,application/xhtml+xml,application/xml)
         * 
         * Массив соответствия Zend\View\Model\<представление> => array(<Header[Accept]>) 
         * передаётся в __invoke($matchAgainst) в качестве первого параметра $matchAgainst
         */      
        $matchAgainst = array(
            // Zend\View\Model\<представление>
            'Zend\View\Model\JsonModel' => array(
                // Header[Accept]:
                'application/json',
            ),
            'Zend\View\Model\FeedModel' => array(
                'application/rss+xml',
            ),
        );
        
        /*
         * Возвращается та модель представления Zend\View\Model\..., которой будет соответствовать заголовок Header[Accept],
         * Соответствие было прописано и передано в массиве $matchAgainst:
         *      - для Header[Accept]=application/json будет возвращена модель Zend\View\Model\JsonModel
         *      - для Header[Accept]=application/rss+xml будет возвращена модель Zend\View\Model\
         * Если соответствия не было найдено, то возвращается модель вида Zend\View\Model\ViewModel\rss+xml
         */
        $viewModel = $this->acceptableViewModelSelector($matchAgainst);
     
    }
}