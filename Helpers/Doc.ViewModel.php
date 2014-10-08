<?php
/*
 * Хелпер viewModel() позволяет задать или вернуть root или объект текущего вида класса ViewModel
 */
// Получить объект класса ViewModel текущего вида с помощью хелпера viewModel()
$currentView = $this->viewModel()->getCurrent();
// Дополнительные методы:
$this->viewModel()->setCurrent();
$this->viewModel()->hasCurrent();


// Получить root объект класса ViewModel текущего вида с помощью хелпера viewModel()
$currentRoot =  $this->viewModel()->getRoot();
$this->viewModel()->setRoot();
$this->viewModel()->hasRoot();
