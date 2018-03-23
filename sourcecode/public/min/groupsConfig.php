<?php

/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */
/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 * */
return array(
    'vphomejs' => array('//js/jquery/jquery-1.10.1.min.js','//js/lib/jquery-ui-1.10.4.custom.min.js', '//js/timjs/timjs-1.3.4.1.js', '//css/timstyle/timstyle-1.3.js', '//js/home/duhoc-home.js'),
    'vphomecss' => array('//css/timstyle/normalize.css','//css/timstyle/slideshow.css','//css/timstyle/slideshow-theme.css','//css/home/smoothness/jquery-ui-1.10.4.custom.css','//css/timstyle/timstyle.core-1.3.css', '//css/timstyle/timstyle.theme-1.3.css', '//css/home/duhoc-home-base.css', '//css/home/duhoc-home-elements.css', '//css/home/duhoc-home-theme.css'),
    //header
    'headerjs' => array('//js/home/welcome/vm-header.js'),
    // view project - screen module
    'vprojjs' => array('//js/home/dialog/vm-dialog-popup.js', '//js/home/screen/vm-screen-link.js', '//js/home/screen/vm-screen-draw.js', '//js/home/screen/vm-screen-view.js', '//js/home/screen/vm-screen-manager.js', '//js/home/share/vm-share.js', '//js/home/share/vm-share-cnu.js', '//js/home/screen/vm-issue.js'),
    //task
    'taskjs' => array('//js/home/task/vm-task-list.js'),
    //list project
    'lprojjs' => array('//js/home/share/vm-share.js', '//js/home/share/vm-share-cnu.js', '//js/home/project/vm-project-list.js'),
    // user
    'userjs' => array('//js/home/user/vm-user-list.js'),
    //home
    'indexjs' => array('//js/home/share/vm-share.js', '//js/home/share/vm-share-cnu.js', '//js/home/screen/vm-screen-manager.js', '//js/home/dialog/vm-dialog-popup.js')
);
