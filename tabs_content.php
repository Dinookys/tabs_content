<?php 

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

class plgContentTabs_Content extends JPlugin {

	protected $autoloadLanguage = true;
	protected $items;

	public function onContentPrepare($context, &$article, &$params, $limitstart=0){

		$document = JFactory::getDocument();    	
    	$document->addStyleSheet(JURI::base( true ) . '/plugins/content/tabs_content/vendor/bootstrap/bootstrap_tabs.min.css');

    	if(JVERSION >= 3){
    		// Check if Jquery Library was called
	    	if(!array_key_exists(JURI::base( true ) . '/media/jui/js/jquery.js', $document->_scripts)){
	    		$document->addScript(JURI::base( true ) . '/media/jui/js/jquery.js');
	    		$document->addScript(JURI::base( true ) . '/media/jui/js/jquery-noconflict.js');
	    		$document->addScript(JURI::base( true ) . '/media/jui/js/jquery-migrate.js');
	    	}
    	}    	

    	$document->addScript(JURI::base( true ) . '/plugins/content/tabs_content/vendor/bootstrap/bootstrap_tabs.min.js');
		
		$openTag = '/(\[tabcontent\s*[^\]]+)([\]]{1})/';
		$closeTag = '/(\[\/tabcontent\s*?\])/';	
		$tabmenu = '/(\[tabmenu\s*?\/\])/';

		preg_match_all($openTag, $article->text, $m);

		$count_matches = count($m[0]);
		$article->text = preg_replace($tabmenu, $this->createTabNav($m[0]), $article->text);

		foreach ($m[0] as $key => $match) {
			if($key == 0){
				$article->text = preg_replace($openTag, '<div class="tab-content" ><div id="tab-'. $key .'" role="tabpanel" class="tab-pane active" >', $article->text, 1);	
			}else{
				$article->text = preg_replace($openTag, '<div id="tab-'. $key .'" role="tabpanel" class="tab-pane" >', $article->text, 1);	
			}
			if($key == $count_matches - 1){
				$article->text = preg_replace($closeTag, '</div></div><!--'. $key .'-->', $article->text, 1);	
			}else{
				$article->text = preg_replace($closeTag, '</div><!--'. $key .'-->', $article->text, 1);			
			};
		}		

		return true;
	}	

	private function createTabNav($m){
		$html = '<ul class="nav nav-tabs" role="tablist">';
		foreach ($m as $key => $match) {
			
			$clear = str_replace(array('[tabcontent ', ']'), '', $match);
			$short_array = explode('="', $clear);
			$shorts = array($short_array[0] => str_replace('"', '', $short_array[1]));
			if($key == 0){
				$html .= '<li class="active"><a href="#tab-'. $key .'" aria-controls="tab-'. $key .'" role="tab" data-toggle="tab" >'. $shorts['title'] .'</a></li>';
			}else{
				$html .= '<li><a href="#tab-'. $key .'" aria-controls="tab-'. $key .'" role="tab" data-toggle="tab" >'. $shorts['title'] .'</a></li>';
			}
			
		}
		$html .= '</ul>';
		return $html;
	}
}